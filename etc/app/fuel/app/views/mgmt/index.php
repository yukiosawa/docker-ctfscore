<?php echo Asset::js('jquery-2.1.1.min.js'); ?>
<?php echo Asset::js('socket.io.js'); ?>

<?php echo Asset::css('animate.css'); ?>
<?php echo Asset::js('jquery.lettering-0.6.min.js'); ?>
<?php echo Asset::js('jquery.textillate.js'); ?>

<style type='text/css'>
  #overlay{
  display : none;
  width: 100%;
  height: 100%;
  text-align: center;
  position: fixed;
  top: 0;
    /* z-index: 100; */
  background: rgba(0,0,0,1);
  }

  #overlayText{
  font-size: 60px;
  color: rgba(255,0,0,1);
  padding-top: 100px;
  vertical-align: middle;
  font-weight: bold;
  }

 body{
  background: rgba(0,0,0,0.8);
 }

 #messageArea{
  color: #32CD32;
  font-weight: bold;
 }
</style>

<script>
 var socket = io(<?php echo '"http://'.$_SERVER['SERVER_NAME'].':8080"'; ?>);
 var overlayTimer;

 socket.on('message', function (data) {
     console.log(data);
     addMessage('messageArea', 'message', data);
 });

 socket.on('success', function (data) {
     console.log(data);
     playAudio('audio-success');
     showOverlay(data);
     addMessage('messageArea', 'success', data);
 });

 socket.on('fail', function (data) {
     console.log(data);
     playAudio('audio-fail');
     //addMessage('messageArea', 'fail', data);
 });

 function addMessage(targetName, className, data){
     var div = $('<div>');
     var span1 = $('<span>');
     var span2 = $('<span>');
     span1.attr('class', 'datetime');
     span1.text(new Date().toTimeString() + ': ');
     span2.attr('class', className);
     span2.text(data);
     div.append(span1).append(span2);
     $('#' + targetName).prepend(div);
     messageTextillate();
     //setTimeout('messageTextillate()', 5000);
 }
 
 function sendMessage(){
     var msg = $(':text[name="chatMessage"]').val();
     if (msg == '') {return};
     $(':text[name="chatMessage"]').val('');
     socket.emit('message', msg);
     addMessage('messageArea', 'message', msg);
 }

 function playAudio(id){
     var target = $('#'+id).get(0);
     if (target){
	 target.currentTime = 0;
	 target.play();
     }
 }

 function showOverlay(data){
     clearTimeout(overlayTimer);
     $('#overlayText').remove();
     var div = $('<div>').attr('id', 'overlayText').text(data);;
     $('#overlay').append(div);
     $('#overlay').fadeIn();
     $('#overlayText').textillate();
     overlayTimer = setTimeout('closeOverlay()', 5000);
 }
 
 function closeOverlay(){
     $('#overlay').fadeOut();
 }

 function messageTextillate(){
     var tlt = $('#messageArea > *').textillate({
	 loop: true,
	 minDisplayTime: 30000,
	 in:{
	     effect: 'fadeInDownBig',
	     shuffle: true
	 },
	 out:{
	     effect: 'fadeOutDownBig',
	     shuffle: true
	 }
     });
     tlt.textillate('stop');
     tlt.textillate('start');
 }
</script>

<form>
  <input type='text' name='chatMessage'></input>
  <input onclick='sendMessage(); return false;' type='submit' value='Message'></input>
</form>

<div id='messageArea'></div>

<?php
  Config::load('ctfscore', true);
  if (Config::get('ctfscore.sound.is_active_on_success')){
    $file = Config::get('ctfscore.sound.success_file');
    echo Html::audio($file, 'id=audio-success');
  }
  if (Config::get('ctfscore.sound.is_active_on_fail')){
    $file = Config::get('ctfscore.sound.fail_file');
    echo Html::audio($file, 'id=audio-fail');
  }
?>

<div id='overlay'></div>

