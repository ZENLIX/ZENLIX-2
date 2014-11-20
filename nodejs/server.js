

var mysql = require('mysql');






//var https = require('https'),     
//    fs =    require('fs');
//rustem openssl req -new -newkey rsa:2048 -nodes -keyout localhost.key -out localhost.csr
//rustem openssl x509 -req -days 365 -in localhost.csr -signkey localhost.key -out localhost.crt

/*
var options = {
    key:    fs.readFileSync('localhost.key'),
    cert:   fs.readFileSync('localhost.crt')
};
var app = https.createServer(options);
var io = require('/usr/local/lib/node_modules/socket.io').listen(app);
app.listen(8080, "0.0.0.0");



*/


var io = require('/usr/local/lib/node_modules/socket.io').listen(8080);


//insert into `notification_msg_pool` (delivers_id, type_op) values ('7371a131b959f3527cbde59f0e5caf96', 'ticket_create');
db.connect(function(err){
    if (err) console.log(err)
});
//setInterval(function(){ io.sockets.emit('timer_sec', {'text':'Таймер прошел!'}) },         10000);



var initial_result;

setInterval(function(){

    db.query('select * from notification_msg_pool', function(err, rows, fields) {
        //if(err) { throw new Error('Failed');}
        
        
        if (rows.length  > 0) {
        
				//type_op=rows[0].type_op; //store values in a variable
				//console.log(singer_name);

				for (var i in rows) {
        //console.log('Post Titles: ', rows[i].post_title);
        
        /*
        
        */
        
        		var p=rows[i].id;
				io.sockets.in(rows[i].delivers_id).emit('new_msg', {type_op: rows[i].type_op, t_id: rows[i].ticket_id, chat_id: rows[i].chat_msg_id});
				db.query('delete from notification_msg_pool WHERE ID = ?', [p])
				}
    
    
    
       //io.sockets.in('user1@example.com').emit('new_msg', {msg: 'hello'});

}
    });




}, 2000); 





io.sockets.on('connection', function (socket) {



socket.on('join', function (data) {
    socket.join(data.uniq_id); // We are using room of socket io
    //io.sockets.in('user.125').emit('new_message', {text: "Hello world"})
    //io.sockets.in('user1@example.com').emit('new_msg', {msg: 'hello'});
  });
  
    /*
    socket.on('my event', function (msg) {
        console.log("DATA!!!");
    });
    */
});