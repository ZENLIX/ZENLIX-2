<?php // SMSC.UA API (smsc.ua) версия 1.3 (20.10.2014)

define("SMSC_HOST", "smpp.smscentre.com");    // адрес SMPP-сервера
define("SMSC_PORT", 3700);                // порт подключения
define("SMSC_LOGIN", get_conf_param('smsc_login'));        // логин клиента
define("SMSC_PASSWORD", get_conf_param('smsc_pass'));    // пароль
define("SMSC_CHARSET", "utf-8");    // кодировка сообщения: utf-8, koi8-r или windows-1251 (по умолчанию)

class SMSC_SMPP {
    private $socket;
    private $sequence_number = 1;

    public function __construct()
    {
        $ip = gethostbyname(SMSC_HOST);

        if ($ip == SMSC_HOST) // dns fail
            $ip = "212.24.33.196"; // fixed ip

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (!$this->socket || !socket_connect($this->socket, $ip, SMSC_PORT))
            throw new Exception(socket_strerror(socket_last_error()));

        if (!$this->bind())
            throw new Exception("Bind error");
    }

    public function __destruct()
    {
        if ($this->socket) {
            $this->unbind();
            socket_close($this->socket);
        }
    }

    private function send_pdu($pdu)
    {
        $length = strlen($pdu);

        if ($this->socket && socket_write($this->socket, $pdu, $length) == $length) {
            $reply = unpack("Nlen/Ncmd_id/Nstatus/Nseq/a*data", $this->read_pdu());

            if ($reply['seq'] == $this->sequence_number++ && $reply['status'] == 0) // ok
                return $reply['data'];
        }

        return false;
    }

    private function read_pdu()
    {
        $pdu = "";
        $wait_sec = 4;

        while (socket_recv($this->socket, $pdu, 16, MSG_WAITALL) != 16 && --$wait_sec >= 0)
            sleep(1);

        if ($wait_sec >= 0) {
            $header = unpack("N4", $pdu);
            $pdu .= socket_read($this->socket, $header[1] - 16); // body
        }

        return $pdu;
    }

    private function bind($system_type = '')
    {
        $pdu = pack("a".strlen(SMSC_LOGIN)."xa".strlen(SMSC_PASSWORD)."xa".strlen($system_type)."xCCCx", SMSC_LOGIN, SMSC_PASSWORD, $system_type, 0x34, 5, 1); // body
        $pdu = pack("NNNN", strlen($pdu) + 16, 0x02/*BIND_TRANSMITTER*/, 0, $this->sequence_number).$pdu; // header + body

        return $this->send_pdu($pdu);
    }

    public function unbind()
    {
        $pdu = pack("NNNN", 16, 0x06/*UNBIND*/, 0, $this->sequence_number);
        $this->send_pdu($pdu);
    }

    // Функция отправки SMS
    //
    // обязательные параметры:
    //
    // $phones - список телефонов через запятую или точку с запятой
    // $message - отправляемое сообщение
    //
    // необязательные параметры:
    //
    // $sender - имя отправителя (Sender ID). Для отключения Sender ID по умолчанию необходимо в качестве имени
    // передать пустую строку или точку.

    public function send_sms($phone, $message, $sender = ".", $valid = "") // $message в кодировке SMSC_CHARSET
    {
        if (preg_match('/[`\x80-\xff]/', $message)) { // is UCS chars
            $message = iconv(SMSC_CHARSET, "UTF-16BE", $message);
            $coding = 2; // UCS2
        }
        else
            $coding = 1; // 8bit

        $sm_length = strlen($message);

        if ($valid) {
            $valid = min((int)$valid, 24 * 60);
            $valid = sprintf('0000%02d%02d%02d00000R', (int)($valid / 1440), ($valid % 1440) / 60, $valid % 60);
        }

        $pdu = pack("xCCa".strlen($sender)."xCCa".strlen($phone)."xCCCa1a".strlen($valid)."xCCCCCnna".$sm_length, // body
                        5,            // source_addr_ton
                        1,            // source_addr_npi
                        $sender,    // source_addr
                        1,            // dest_addr_ton
                        1,            // dest_addr_npi
                        $phone,        // destination_addr
                        0,            // esm_class
                        0,            // protocol_id
                        3,            // priority_flag
                        "",            // schedule_delivery_time
                        $valid,        // validity_period
                        0,            // registered_delivery_flag
                        0,            // replace_if_present_flag
                        $coding * 4,// data_coding
                        0,            // sm_default_msg_id
                        0,            // sm_length + short_message [empty]
                        0x0424,        // TLV message_payload tag
                        $sm_length, // message length
                        $message    // message
                    );

        $pdu = pack("NNNN", strlen($pdu) + 16, 0x04/*SUBMIT_SM*/, 0, $this->sequence_number).$pdu; // header + body

        return $this->send_pdu($pdu); // message id or false on error
    }
}

// Examples:
// include "smsc_smpp.php";
// try {
//        $S = new SMSC_SMPP();
//        $S->send_sms("79999999999", "test message", "sender");
// }
// catch (Exception $e) {
//        echo $e->getMessage();
// }

?>