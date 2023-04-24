<?php
# By : h.muaed #
error_reporting(0);
header('Content-Type: application/json', false);
if (function_exists('fastcgi_finish_request')) {
fastcgi_finish_request();
}
ob_start();
$API_KEY = "6292808836:AAFiXGYL_nnOvrrIK9_V1Tt4I1KI3AHtZhA";
$Amind = 5707831441;
define('Admin',$Amind);
define('API_KEY',$API_KEY);
define('IDBot', explode(":", API_KEY)[0]);
echo file_get_contents("https://api.telegram.org/bot".API_KEY."/setwebhook?url=".$_SERVER['SERVER_NAME']."".$_SERVER['SCRIPT_NAME']);
function bot($method,$database=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$database);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}
function getUrlContent($url){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$data = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
return ($httpcode>=200 && $httpcode<300) ? $data : false;
}
function CheckChannels($from_id){
$vv = true;
$stas = json_decode(file_get_contents("settings.json"),true);
if (count($stas["Channels"]) != 0){
$channels = $stas["Channels"];
foreach ($channels as $channel){
$id = $channel['id'];
$getChatMemberReq = file_get_contents("https://api.telegram.org/bot" . API_KEY . "/getChatMember?chat_id=" . $id . "&user_id=" . $from_id);
$getChatMemberRes = json_decode($getChatMemberReq, true);
if(strstr($getChatMemberReq,'"left"') or strstr($getChatMemberReq,'"USER_ID_INVALID"') or strstr($getChatMemberReq,'"kicked"') or strstr($getChatMemberReq,'"user not found"') or strstr($getChatMemberReq,'"Bad Request"')){
#if ($getChatMemberRes['result']['status'] == "left" or $getChatMemberRes['result']['status'] == "kicked"){
$type = $channel['type'];
$link = $channel['link'];
if($type == "private"){
bot('sendmessage', ['chat_id' => $from_id, 'text' => "⌔︙عذراً عليك الأشتراك في قناة البوت أولاً،
⌔︙ القناة : [$link]($link)", 'disable_web_page_preview' => 'true', 'parse_mode' => 'Markdown', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => $channel['title'], 'url' => $link]]]]) ]);
}else{
bot('sendmessage', ['chat_id' => $from_id, 'text' => "⌔︙عذراً عليك الأشتراك في قناة البوت أولاً،
⌔︙ القناة : [@$link](t.me/$link)", 'parse_mode' => 'Markdown', 'disable_web_page_preview' => 'true', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => $channel['title'], 'url' => "t.me/".$link]]]])]);  
}
$vv = false;
break;
}
}
}
return $vv;
}
function GetFile($file_id){
return bot('getFile', [
'file_id' => $file_id
]);
}
function File_path($file_path){
$info = file_get_contents("https://api.telegram.org/file/bot" . API_KEY . "/" . $file_path);
return $info;
}
function ViewChannels($chatId, $type, $message_id = null){
$status = json_decode(file_get_contents("settings.json"),true);
if (count($status["Channels"]) != 0){
$rows = array_chunk($status["Channels"], 2);
$i = 0;
$x = 0;
$keyboard = [];
$keyboard["inline_keyboard"] = [];
foreach ($rows as $row){
$j = 0;
$keyboard["inline_keyboard"][$i] = [];
$bottons = $row;
foreach ($bottons as $botton){
$Ibotton = ["text" =>$botton['title'], "callback_data" => 'csdel+' . $x];
$keyboard["inline_keyboard"][$i][$j] = $Ibotton;
$j++;
$x++;
}
$i++;
}
$Ibotton = ["text" => "➕", "callback_data" => 'csaddChannel'];
$keyboard["inline_keyboard"][$i][] = $Ibotton;
$Ibotton = ["text" => "رجوع", "callback_data" => 'Cancel'];
$keyboard["inline_keyboard"][$i + 1][] = $Ibotton;
}else{
$keyboard["inline_keyboard"]=[];
$keyboard["inline_keyboard"][]=[['text'=>" ➕",'callback_data'=>'csaddChannel']];
$keyboard["inline_keyboard"][]=[['text'=>" رجوع",'callback_data'=>'Cancel']];
}
$reply_markup = json_encode($keyboard);
if (count($status["Channels"]) != 0){
$messageText = "قنوات الاشتراك الإجبارية
انقر فوق اسم القناة لحذفه 🗑";
}else{
$messageText = "لا توجد قنوات للاشتراك الإجباري
لإضافة قناة ، اضغط على (➕)";
}
if ($type == 'send'){
bot('sendmessage', ['chat_id' => $chatId, 'text' => $messageText, 'disable_web_page_preview' => true, 'reply_markup' => $reply_markup]);
}else{
bot('editmessagetext', ['chat_id' => $chatId, 'message_id' => $message_id, 'text' => $messageText, 'disable_web_page_preview' => true, 'reply_markup' => $reply_markup]);
}
}
function ViewAdminsh($chatId, $type, $message_id = null){
$status = json_decode(file_get_contents("Adminsh.json"),true);
if (count($status["admin"]) != 0){
$rows = array_chunk($status["admin"], 2);
$i = 0;
$x = 0;
$keyboard = [];
$keyboard["inline_keyboard"] = [];
foreach ($rows as $row){
$j = 0;
$keyboard["inline_keyboard"][$i] = [];
$bottons = $row;
foreach ($bottons as $botton){
$Ibotton = ["text" =>$botton['title'], "callback_data" => 'admindel+' . $x];
$keyboard["inline_keyboard"][$i][$j] = $Ibotton;
$j++;
$x++;
}
$i++;
}
$Ibotton = ["text" => "➕", "callback_data" => 'csaddadmin'];
$keyboard["inline_keyboard"][$i][] = $Ibotton;
$Ibotton = ["text" => "رجوع", "callback_data" => 'Cancel'];
$keyboard["inline_keyboard"][$i + 1][] = $Ibotton;
}else{
$keyboard["inline_keyboard"]=[];
$keyboard["inline_keyboard"][]=[['text'=>" ➕",'callback_data'=>'csaddadmin']];
$keyboard["inline_keyboard"][]=[['text'=>" رجوع",'callback_data'=>'Cancel']];
}
$reply_markup = json_encode($keyboard);
if (count($status["admin"]) != 0){
$messageText =  "الادمنيه المرفوعين بالبوت
انقر فوق اسم الادمن لتنزيله 🗑";
}else{
$messageText = "لا يوجد ادمنيه في البوت
لإضافة ادمن ، اضغط على (➕)";
}
if ($type == 'send'){
bot('sendmessage', ['chat_id' => $chatId, 'text' => $messageText, 'disable_web_page_preview' => true, 'reply_markup' => $reply_markup]);
}else{
bot('editmessagetext', ['chat_id' => $chatId, 'message_id' => $message_id, 'text' => $messageText, 'disable_web_page_preview' => true, 'reply_markup' => $reply_markup]);
}
}
function Sv($a,$b){file_put_contents($a,json_encode($b,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));}
function Save($a){file_put_contents("media.json",json_encode($a,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));}
#--------
$update = file_get_contents("php://input");
$updateData = json_decode($update,true);
$messageData = isset($updateData["callback_query"]) ? $updateData["callback_query"] : $updateData["message"];
$messageTime = $messageData["date"];
$chatId = isset($updateData["callback_query"]) ? $updateData["callback_query"]["message"]["chat"]["id"] : $updateData["message"]["chat"]["id"];
$chatName = isset($updateData["callback_query"]) ? $updateData["callback_query"]["message"]["chat"]["title"] : $updateData["message"]["chat"]["title"];
$chatType = isset($updateData["callback_query"]) ? $updateData["callback_query"]["message"]["chat"]["type"] : $updateData["message"]["chat"]["type"];
$messageId = isset($updateData["callback_query"]) ? $updateData["callback_query"]["message"]["message_id"] : $updateData["message"]["message_id"];
$messageText = $messageData["text"];
$reply = $messageData["reply_to_message"];
$replyID = $messageData["reply_to_message"]["from"]["id"];
$replyName = $messageData["reply_to_message"]["from"]["first_name"];
$replyUsername = $messageData["reply_to_message"]["from"]["username"];
$data = $updateData["callback_query"]["data"];
$from_id = $messageData["from"]["id"];
$from_name = $messageData["from"]["first_name"] . " " . $messageData["from"]["last_name"];
$from_username = isset($messageData["from"]["username"]) ? $messageData["from"]["username"] : "لا يوجد";
$info_id ="⌔︙الاسم : ↫ ❨ $from_name ❩\n⌔︙المعرف : ↫ ❨ $from_username ❩\n⌔︙الايدي : ↫ ❨ $from_id ❩\n — — — — — — — — — ";
$forward = $messageData["forward_from"];
$forwardFromChat = $messageData["forward_from_chat"];
$caption = $messageData['caption'];
$document = $messageData["document"];
$document_file_id = $messageData["document"]["file_id"];
$document_file_name = $messageData["document"]["file_name"];
$callbackid = $updateData['callback_query']['id'];
$TokenInfo = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getMe"));
$UserBot = $TokenInfo->result->username; 
$NameBot = $TokenInfo->result->first_name; 
$IdBot = $TokenInfo->result->id;
$status = json_decode(file_get_contents("settings.json"),true);
$IDChatMember = json_decode(file_get_contents("Member.json"),true);
$adminv = json_decode(file_get_contents("Adminsh.json"),true);
$media  = json_decode(file_get_contents("media.json"),true);
$t=json_decode(file_get_contents("t.json"),1);
$button['inline_keyboard'][] = [['text' =>'الغاء', 'callback_data' =>'Cancel']];
$keyboard2 = json_encode($button);
$Ty = $media['type'][$chatId];
$Ch = $media['ch'];
function is_deved($user){
global $Amind;
global $adminv;
if($user == $Amind){
$is_dfe = true;
}elseif(count($adminv["admin"]) != 0){
$adminvls = $adminv["admin"];
foreach ($adminvls as $adm){
if($user == $adm['id']){
$is_dfe = true;
}
}
}else{
$is_dfe = false;
}
return $is_dfe;
}
$input = json_decode($update);
if(is_deved($from_id)  and $chatType == "private"){
if ($messageText == '/stop'){
$input->admin = $chatId;
$input->param = 'stop';
$req = 'https://botk5.net/Api/botat/?token='.API_KEY.'&update='.urlencode(json_encode($input));
$res = file_get_contents($req);
$jes = json_decode($res);
bot('sendmessage',[
'chat_id' =>$chatId,
'text' => $jes->msg,
'parse_mode' => "MarkDown",
'disable_web_page_preview' => true
]);
}
if (!isset($status["bord"])){
$status["bord"] = "off";
file_put_contents("settings.json",json_encode($status,128|32|256));
}
if ($status["bord"] != 'off') {
$input->admin = $chatId;
$input->param = 'post';
$input->stop = "/stop";
$input->script_path = $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
$memb = "memb.txt";
$p = ( $_SERVER['SERVER_NAME'] . "" . $_SERVER['SCRIPT_NAME']);
$remove_http = preg_replace("#\bhttps?://[^,\s()<>]#", "", $p);
$page_name = explode('/', $remove_http);
$last_index = $page_name[count($page_name)-1];
$content = explode('.', $last_index);
$script_file = $content[0].".".$content[1];
$path_ = str_replace($script_file,"memb.txt",$p);
if (isset($status["bord"]) and $status["bord"] == 'fwd') {
$input->post_type = 'fwd';
$req = 'https://botk5.net/Api/botat/?token='.API_KEY.'&update='.urlencode(json_encode($input)).'&path='.urlencode($path_);
$res = getUrlContent($req);
}elseif (isset($status["bord"]) and $status["bord"] == 'msg') {
$input->post_type = 'msg';
$req = 'https://botk5.net/Api/botat/?token='.API_KEY.'&update='.urlencode(json_encode($input)).'&path='.urlencode($path_);
$res = getUrlContent($req);
}
$status["bord"] = "off";
file_put_contents("settings.json",json_encode($status,128|32|256));
return;
} elseif($status["bord"] == 'on') {
bot('sendmessage',['chat_id' =>$chatId,'text' => 'هنالك عملية اذاعة حاليا..']);
return;
}
if($messageText == "/start"){ 
unset($status[$chatId]);
unset($status['channelsIdPv']);
unset($status['channelstitlePv']);
file_put_contents("settings.json",json_encode($status,128|32|256));
if ($status['Tws']){
$TwsOFFON = "✅";
}else{
$TwsOFFON = "❎";
}
if ($status['alert']){
$alertOFFON = "✅";
}else{
$alertOFFON = "❎";
}
$Ok = $status['ok'];
if ($Ok){
$OFFON = "✅";
}else{
$OFFON = "❎";
}
bot('sendmessage',[
'chat_id' => $chatId,
'text' => "اهلا بك في لوحة التحكم البوت :",
'disable_web_page_preview' => 'true',
'parse_mode' => 'Markdown',
'reply_markup' =>json_encode(['inline_keyboard' => [
[['text'=>"الاشتراك الوهمي",'callback_data'=>"ChaneLf"]],
[['text' => ': حالة البوت', 'callback_data' => '#'],['text' => $OFFON, 'callback_data' => 'statusOFFON']],
[['text' =>': التنبيه', 'callback_data' => '##'],['text' =>$alertOFFON, 'callback_data' => 'alertOFFON']],
[['text' =>': التواصل', 'callback_data' => '###'],['text' =>$TwsOFFON, 'callback_data' => 'TwsOFFON']],
[['text' => 'الأحصائيات', 'callback_data' => 'statistics']],
[['text' => 'الأشتراك الاجباري', 'callback_data' => 'cs'],['text' => 'الاذاعه', 'callback_data' => 'sender']],
[['text' => 'الادمنيه', 'callback_data' => 'Adminsh']],
[['text' => 'رفع نسخه احتياطيه', 'callback_data' => 'UpCURLFile'],['text' => 'جلب نسخه احتياطيه', 'callback_data' => 'sendfile']],
]])
]);
}
if ($data == "Cancel"){
unset($status[$chatId]);
unset($status['channelsIdPv']);
unset($status['channelstitlePv']);
file_put_contents("settings.json",json_encode($status,128|32|256));
if ($status['Tws']){
$TwsOFFON = "✅";
}else{
$TwsOFFON = "❎";
}
if ($status['alert']){
$alertOFFON = "✅";
}else{
$alertOFFON = "❎";
}
$Ok = $status['ok'];
if ($Ok){
$OFFON = "✅";
}else{
$OFFON = "❎";
}
bot('editMessageText',[
'chat_id'=>$chatId,
'message_id' => $messageId,
'text' => "اهلا بك في لوحة التحكم البوت :",
'disable_web_page_preview' => 'true',
'parse_mode' => 'Markdown',
'reply_markup' =>json_encode(['inline_keyboard' => [
[['text'=>"الاشتراك الوهمي",'callback_data'=>"ChaneLf"]],
[['text' => ': حالة البوت', 'callback_data' => '#'],['text' => $OFFON, 'callback_data' => 'statusOFFON']],
[['text' =>': التنبيه', 'callback_data' => '##'],['text' =>$alertOFFON, 'callback_data' => 'alertOFFON']],
[['text' =>': التواصل', 'callback_data' => '###'],['text' =>$TwsOFFON, 'callback_data' => 'TwsOFFON']],
[['text' => 'الأحصائيات', 'callback_data' => 'statistics']],
[['text' => 'الأشتراك الاجباري', 'callback_data' => 'cs'],['text' => 'الاذاعه', 'callback_data' => 'sender']],
[['text' => 'الادمنيه', 'callback_data' => 'Adminsh']],
[['text' => 'رفع نسخه احتياطيه', 'callback_data' => 'UpCURLFile'],['text' => 'جلب نسخه احتياطيه', 'callback_data' => 'sendfile']],
]])
]);
}
if($data == "DelStart"){
	if($status["start"]){
unset($status["start"]);
file_put_contents("settings.json",json_encode($status,128|32|256));
bot('editMessageText', ['chat_id' => $chatId, 'message_id' => $messageId, 'text' => 'تم حذف الرساله الجديده بنجاح', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Back', 'callback_data' => 'Cancel']]]]) ]);
}else{
bot('answerCallbackQuery', ['callback_query_id' => $callbackid, 'text' => "لم تقم باضافه رساله جديده ل start",'show_alert'=>true, ]);
}
}
if($status[$chatId] == "AddStart" and !$data){
unset($status[$chatId]);
$status["start"] = $messageText;
file_put_contents("settings.json",json_encode($status,128|32|256));
bot('sendmessage', ['chat_id' => $chatId, 'text' => 'تم حفظ الرساله الجديده ل /start بنجاح!', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Back', 'callback_data' => 'Cancel']]]]) ]);
}
if($data == "AddStart"){
$status[$chatId] = "AddStart";
file_put_contents("settings.json",json_encode($status,128|32|256));
bot('editMessageText', ['chat_id' => $chatId, 'message_id' => $messageId, 'text' => 'ارسال الرساله الجديده الان', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'الغاء', 'callback_data' => 'Cancel']]]]) ]);
}
if($status[$chatId] == "UpCURLFile" and !$data){
if ($document) {
$file_name = $document_file_name == "settings.json" || $document_file_name == "Member.json";
if ($file_name == true) {
$getfile = GetFile($document_file_id)->result->file_path;
$file = File_path($getfile);
if (file_exists("$document_file_name")) {
unlink("$document_file_name");
}
file_put_contents("$document_file_name",$file);
bot('sendmessage', ['chat_id' => $chatId, 'text' => 'تم رفع الملف بنجاح', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Back', 'callback_data' => 'Cancel']]]]) ]);
unset($status[$chatId]);
file_put_contents("settings.json",json_encode($status,128|32|256));return false;
} else {
bot('sendmessage', ['chat_id' => $chatId, 'text' => 'عذرا اسم الملف غير مطابق', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Back', 'callback_data' => 'Cancel']]]]) ]);return false;
}
} else {
bot('sendmessage', ['chat_id' => $chatId, 'text' => 'عذرا ارسل ملف فقط.!', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Back', 'callback_data' => 'Cancel']]]]) ]);return false;
}
}
if($data == "UpCURLFile") {
bot('deleteMessage',['chat_id'=>$chatId,'message_id'=>$messageId]);
$status[$chatId] = "UpCURLFile";
file_put_contents("settings.json",json_encode($status,128|32|256));
bot('sendmessage', ['chat_id' => $chatId, 'text' => 'قم بأرسال الملف الان تأكد من ان يكون اسم الملف ( `settings.json` ) او (`Member.json`)', 'parse_mode' => 'Markdown','reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'الغاء', 'callback_data' => 'Cancel']]]]) ]);
}
if($data == "sendfile"){
bot('deleteMessage',['chat_id'=>$chatId,'message_id'=>$messageId]);
if ($status['Tws']){
$TwsOFFON = "✅";
}else{
$TwsOFFON = "❎";
}
if ($status['alert']){
$alertOFFON = "✅";
}else{
$alertOFFON = "❎";
}
$Ok = $status['ok'];
if ($Ok){
$OFFON = "✅";
}else{
$OFFON = "❎";
}
bot('senddocument',[
'chat_id'=>$chatId,
'document'=>new CURLFile("memb.txt"),
]);
bot('senddocument',[
'chat_id'=>$chatId,
'document'=>new CURLFile("settings.json"),
"caption"=>"البوت : @".$UserBot."\nالاعضاء : ".count(explode("\n",file_get_contents("memb.txt")))."\nحاله البوت : ".$OFFON."\nالتنبيه : ".$alertOFFON."\nالتواصل : ".$TwsOFFON,
'disable_web_page_preview' => 'true',
'parse_mode' => 'Markdown',
'reply_markup' =>json_encode(['inline_keyboard' => [[['text' => 'رفع نسخه', 'callback_data' => 'UpCURLFile']]]])]);
}
if ($data == "statistics"){
bot('editMessageText', [
'chat_id' => $chatId, 
'message_id' => $messageId, 
'text' =>"⌔︙*الأحصائيات:* ↫\nالاعضاء :↫❨ `".count(explode("\n",file_get_contents("memb.txt")))."` ❩",
'disable_web_page_preview' => 'true', 
'parse_mode' => 'Markdown', 
'reply_markup' => json_encode([
'inline_keyboard' => [[['text' => 'Back', 'callback_data' => 'Cancel']]]])]);
}
if ($data == "TwsOFFON"){
if ($status['Tws']){
$status['Tws'] = false;
}else{
$status['Tws'] = true;
}
file_put_contents("settings.json",json_encode($status,128|32|256));
if ($status['Tws']){
$TwsOFFON = "✅";
}else{
$TwsOFFON = "❎";
}
if ($status['alert']){
$alertOFFON = "✅";
}else{
$alertOFFON = "❎";
}
if ($status['ok']){
$OFFON = "✅";
}else{
$OFFON = "❎";
}
bot('editMessageText',[
'chat_id'=>$chatId,
'message_id' => $messageId,
'text' => "اهلا بك في لوحة التحكم البوت :",
'disable_web_page_preview' => 'true',
'parse_mode' => 'Markdown',
'reply_markup' =>json_encode(['inline_keyboard' => [
[['text'=>"الاشتراك الوهمي",'callback_data'=>"ChaneLf"]],
[['text' => ': حالة البوت', 'callback_data' => '#'],['text' => $OFFON, 'callback_data' => 'statusOFFON']],
[['text' =>': التنبيه', 'callback_data' => '##'],['text' =>$alertOFFON, 'callback_data' => 'alertOFFON']],
[['text' =>': التواصل', 'callback_data' => '###'],['text' =>$TwsOFFON, 'callback_data' => 'TwsOFFON']],
[['text' => 'الأحصائيات', 'callback_data' => 'statistics']],
[['text' => 'الأشتراك الاجباري', 'callback_data' => 'cs'],['text' => 'الاذاعه', 'callback_data' => 'sender']],
[['text' => 'الادمنيه', 'callback_data' => 'Adminsh']],
[['text' => 'رفع نسخه احتياطيه', 'callback_data' => 'UpCURLFile'],['text' => 'جلب نسخه احتياطيه', 'callback_data' => 'sendfile']],
]])
]);
}
if ($data == "statusOFFON"){
$Ok = $status['ok'];
if ($Ok){
$status['ok'] = false;
}else{
$status['ok'] = true;
}
file_put_contents("settings.json",json_encode($status,128|32|256));
if ($status['Tws']){
$TwsOFFON = "✅";
}else{
$TwsOFFON = "❎";
}
if ($status['alert']){
$alertOFFON = "✅";
}else{
$alertOFFON = "❎";
}
$Ok = $status['ok'];
if ($Ok){
$OFFON = "✅";
}else{
$OFFON = "❎";
}
bot('editMessageText',[
'chat_id'=>$chatId,
'message_id' => $messageId,
'text' => "اهلا بك في لوحة التحكم البوت :",
'disable_web_page_preview' => 'true',
'parse_mode' => 'Markdown',
'reply_markup' =>json_encode(['inline_keyboard' => [
[['text'=>"الاشتراك الوهمي",'callback_data'=>"ChaneLf"]],
[['text' => ': حالة البوت', 'callback_data' => '#'],['text' => $OFFON, 'callback_data' => 'statusOFFON']],
[['text' =>': التنبيه', 'callback_data' => '##'],['text' =>$alertOFFON, 'callback_data' => 'alertOFFON']],
[['text' =>': التواصل', 'callback_data' => '###'],['text' =>$TwsOFFON, 'callback_data' => 'TwsOFFON']],
[['text' => 'الأحصائيات', 'callback_data' => 'statistics']],
[['text' => 'الأشتراك الاجباري', 'callback_data' => 'cs'],['text' => 'الاذاعه', 'callback_data' => 'sender']],
[['text' => 'الادمنيه', 'callback_data' => 'Adminsh']],
[['text' => 'رفع نسخه احتياطيه', 'callback_data' => 'UpCURLFile'],['text' => 'جلب نسخه احتياطيه', 'callback_data' => 'sendfile']],
]])
]);
}
if ($data == "alertOFFON"){
$alert = $status['alert'];
if ($alert){
$status['alert'] = false;
}else{
$status['alert'] = true;
}
file_put_contents("settings.json",json_encode($status,128|32|256));
if ($status['Tws']){
$TwsOFFON = "✅";
}else{
$TwsOFFON = "❎";
}
if ($status['alert']){
$alertOFFON = "✅";
}else{
$alertOFFON = "❎";
}
$Ok = $status['ok'];
if ($Ok){
$OFFON = "✅";
}else{
$OFFON = "❎";
}
bot('editMessageText',[
'chat_id'=>$chatId,
'message_id' => $messageId,
'text' => "اهلا بك في لوحة التحكم البوت :",
'disable_web_page_preview' => 'true',
'parse_mode' => 'Markdown',
'reply_markup' =>json_encode(['inline_keyboard' => [
[['text'=>"الاشتراك الوهمي",'callback_data'=>"ChaneLf"]],
[['text' => ': حالة البوت', 'callback_data' => '#'],['text' => $OFFON, 'callback_data' => 'statusOFFON']],
[['text' =>': التنبيه', 'callback_data' => '##'],['text' =>$alertOFFON, 'callback_data' => 'alertOFFON']],
[['text' =>': التواصل', 'callback_data' => '###'],['text' =>$TwsOFFON, 'callback_data' => 'TwsOFFON']],
[['text' => 'الأحصائيات', 'callback_data' => 'statistics']],
[['text' => 'الأشتراك الاجباري', 'callback_data' => 'cs'],['text' => 'الاذاعه', 'callback_data' => 'sender']],
[['text' => 'الادمنيه', 'callback_data' => 'Adminsh']],
[['text' => 'رفع نسخه احتياطيه', 'callback_data' => 'UpCURLFile'],['text' => 'جلب نسخه احتياطيه', 'callback_data' => 'sendfile']],
]])
]);
}
if($status[$chatId] == "csaddChannelpv" and !$data and $messageText){
$channels = json_decode(file_get_contents("settings.json"),true);
$channels["Channels"][] = [
'id'=>$channels['channelsIdPv'],
'title'=>$channels['channelstitlePv'],
'type'=>"private",
"link"=>$messageText
];
file_put_contents("settings.json",json_encode($channels,128|32|256));
ViewChannels($chatId,'send',$messageId);return false;
}
if($status[$chatId] == "csaddChannel" and !$data){
if($forwardFromChat){
$title = $forwardFromChat['title'];
$id = $forwardFromChat['id'];
$UserNameChannel = $forwardFromChat['username'];
$getChatMemberReq = file_get_contents("https://api.telegram.org/bot".$API_KEY."/getChatMember?chat_id=".$forwardFromChat['id']."&user_id=".IDBot);
$getChatMemberRes = json_decode($getChatMemberReq, true);
if($getChatMemberRes['result']['status'] == "administrator"){
if(isset($forwardFromChat['username'])){
unset($status[$chatId]);
file_put_contents("settings.json",json_encode($status,128|32|256));
$channels = json_decode(file_get_contents("settings.json"),true);
$channels["Channels"][] = [
'id'=>$id,
'title'=>$title,
'type'=>"public",
"link"=>$UserNameChannel
];
file_put_contents("settings.json",json_encode($channels,128|32|256));
ViewChannels($chatId,'send',$messageId);return false;
}else{
$status[$chatId] = "csaddChannelpv";
$status['channelstitlePv'] = $forwardFromChat['title'];
$status['channelsIdPv'] = $forwardFromChat['id'];
file_put_contents("settings.json",json_encode($status,128|32|256));
bot('sendmessage', ['chat_id' => $chatId, 'text' => 'ارسل رابط القناة الخاص', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'الغاء', 'callback_data' => 'Cancel']]]])]);return false;
}
}else{
bot('sendmessage', ['chat_id' => $chatId, 'text' => 'عذرا البوت ليس مشرف في القناة', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'الغاء', 'callback_data' => 'Cancel']]]])]);return false;
}
}else{
bot('sendmessage', ['chat_id' => $chatId, 'text' => 'عذرا هذه ليس رسالة توجيه من قناة', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'الغاء', 'callback_data' => 'Cancel']]]])]);return false;
}
}
if($data == "csaddChannel"){
$status[$chatId] = "csaddChannel";
file_put_contents("settings.json",json_encode($status,128|32|256));
bot('editMessageText', ['chat_id' => $chatId, 'message_id' => $messageId, 'text' => 'يرجى ارسال توجيه من القناة ', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'الغاء', 'callback_data' => 'Cancel']]]]) ]);
}
if($data == "cs"){
ViewChannels($chatId,'edit',$messageId);
}
if(preg_match("/^(csdel)([+])(.*)/s", $data)){
preg_match("/^(csdel)([+])(.*)/s", $data, $matcha);
$n = $matcha[3];
unset($status['Channels'][$n]);
$status['Channels']= array_values($status['Channels']); 
file_put_contents("settings.json", json_encode($status,128|32|256));
ViewChannels($chatId,'edit',$messageId);
}
} // end Admin
if(!$status['ok']){
if(!is_deved($from_id) ){
$Replymessage = "⌔︙عذرا البوت  تحت الصيانه الان .";
if($data){
bot('editMessageText',[
'chat_id'=>$chatId,
 'message_id'=>$messageId,
'text'=>$Replymessage,
'parse_mode'=>'Markdown'
]);
}else{
bot('sendmessage',[
'chat_id'=>$chatId,
'text'=>$Replymessage,
'parse_mode'=>'Markdown'
]);
}
return false;
}
}
if($status['alert'] and !is_deved($from_id)  and !$data){
if (!in_array($from_id, explode("\n",file_get_contents("memb.txt")))) {
if($from_id){
file_put_contents("memb.txt", $from_id."\n",FILE_APPEND);
if($from_username == null){
$username_m = "Null";
}else{
$username_m = "[".$from_username."]";
}
$userid_m = "[".$from_id."](tg://user?id=".$from_id.")";
bot('sendmessage',[ 'chat_id'=>$Amind, 'parse_mode'=> 'markdown', 'text'=>"• قام شخص جديد بالدخول الى البوت 🤴،\n\n- الأسم: ".$from_name."،\n- الأيدي: ".$userid_m."،\n-المعرف: ".$username_m."،\n".date("m/d H:i A").".",
]);
}
}
}
if (!in_array($from_id, explode("\n",file_get_contents("memb.txt")))) {
file_put_contents("memb.txt", $from_id."\n",FILE_APPEND);
}
if($chatType == "private"){
if (is_deved($from_id) ){
if($messageText != "/start" && $status[$chatId] == "SerFu" and !$data){
bot('sendmessage',[
'chat_id'=>$chatId,
'text'=>"*• تم الرد على الرساله بنجاح*",'parse_mode'=>"Markdown",'reply_to_message_id'=>$messageId,
'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Back', 'callback_data' => 'Cancel']]]])
]);
bot('copyMessage',[
'chat_id'=>$status[$chatId."for"],
'from_chat_id'=>$chatId, 
'message_id'=>$messageId,
]);
unset($status[$chatId."for"]);
unset($status[$chatId]);
file_put_contents("settings.json",json_encode($status,128|32|256));return false;
}
}
}
$da = explode('#msg', $data);
if(explode('#msg', $data)[0] == "#m"){
bot('deleteMessage',['chat_id'=>$chatId,'message_id'=>$messageId]);
$status[$chatId] = "SerFu";
$status[$chatId."for"] = $da[1];
file_put_contents("settings.json",json_encode($status,128|32|256));
bot('sendmessage', ['chat_id' => $chatId, 'text' =>'• ارسل رسالتك الان (`نص ، ميديا`)', 'parse_mode' => 'Markdown','reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'الغاء', 'callback_data' => 'Cancel']]]]) ]);
}
if($chatType == "private"){
if (!is_deved($from_id) ){
if($messageText != "/start" && $status['Tws'] == true and !$data){
$typefrom_username = isset($from_username) ? $from_username : "لا يوجد";
bot('copyMessage',[
'chat_id'=>$Amind, 
'from_chat_id'=>$from_id, 
'message_id'=>$messageId,
'reply_markup' =>json_encode(['inline_keyboard' => [
[['text' => '↙️ معلومات المرسل ↘️', 'callback_data' => 'Qs']],
[['text' =>$from_name, 'callback_data' =>"'z"]],
[['text' =>$from_username, 'url' =>"t.me/".str_replace("@", "",$typefrom_username)],['text' =>"المعرف :->", 'callback_data' =>"'c"]],
[['text' => 'رد', 'callback_data' => '#m#msg'.$from_id]],
[['text' =>'مسح', 'callback_data' => 'd#']],
]])
]);
}
}
}
if($data == "d#"){
bot('deleteMessage',['chat_id'=>$chatId,'message_id'=>$messageId]);
}
if($data == 'sender'){
bot('editMessageText',[
'chat_id'=>$chatId,
'message_id' => $messageId,
'text' => "يرجى أختيار نوع الأرسال 📮
رسالة عادية : نص أو أي نوع من الوسائط
رسالة بالتوجية : نص موجهه أو وسائط",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "رسالة بالتوجية",'callback_data' => 'broadcast_fwd'],['text' => 'رسالة عادية', 'callback_data' => 'broadcast_msg']],
[['text' => 'الغاء', 'callback_data' => 'Cancel']],
]])
]);
unset($status[$chatId]);
file_put_contents("settings.json",json_encode($status,128|32|256));
}
if ($data == 'broadcast_fwd') {
$status["bord"] = "fwd";
file_put_contents("settings.json",json_encode($status,128|32|256));
    bot('editMessageText',[
        'chat_id' =>$chatId,
        'message_id' =>$messageId,
        'text' => "يرجى توجية من الرسالة أو أي نوع من الوسائط 🚸",
        'parse_mode' => "MarkDown",
        'disable_web_page_preview' => true,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>'الغاء الأذاعة','callback_data'=>'off']],  
            ]
            ])
    ]);
} elseif ($data == 'broadcast_msg') {
$status["bord"] = "msg";
file_put_contents("settings.json",json_encode($status,128|32|256));
    bot('editMessageText',[
        'chat_id' =>$chatId,
        'message_id' =>$messageId,
        'text' => "يرجى أرسال نص أو أرسال أي نوع من الوسائط 🚸",
        'parse_mode' => "MarkDown",
        'disable_web_page_preview' => true,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>'الغاء الأذاعة','callback_data'=>'off']],
            ]
        ])
    ]);
}
if($data == "off" ){ 
unset($status["bord"]);
file_put_contents("settings.json",json_encode($status,128|32|256));
    bot('editMessageText',[
        'chat_id' =>$chatId,
        'message_id' =>$messageId,
    'text'=>"تم ألغاء عملية الاذاعة",
    'reply_markup'=>json_encode([
        'inline_keyboard'=>[
[['text'=>'رجـوع 🔙','callback_data'=>"Cancel"]],    
        ]
    ])
    ]);
}
if($chatId != $Amind){
if($data == "csaddadmin" || $data == "Adminsh"){
bot('answercallbackquery',[
'callback_query_id'=>$callbackid,
'text'=>"• الامر للمطور الاساسي فقط",
]);
}
}
if($chatId == $Amind){
if($status[$chatId] == "csaddadmin" and !$data){
if(preg_match("/^[0-9]+$/",$messageText)){
$getChatMemberReq = file_get_contents("https://api.telegram.org/bot".$API_KEY."/getChat?chat_id=".$messageText);
$getChatMemberRes = json_decode($getChatMemberReq, true);
if($getChatMemberRes['result']['id']){
unset($status[$chatId]);
file_put_contents("settings.json",json_encode($status,128|32|256));
$adminv["admin"][] = [
'id'=>$getChatMemberRes['result']['id'],
'title'=>$getChatMemberRes['result']['first_name'],
'type'=>"public",
"link"=>$getChatMemberRes['result']['username']
];
file_put_contents("Adminsh.json",json_encode($adminv,128|32|256));
ViewAdminsh($chatId,'send',$messageId);return false;
}else{
bot('sendmessage', ['chat_id' => $chatId, 'text' => 'عذرا اما الرساله ليست ايدي او ان صاحب الايدي لا يستخدم البوت ( حاظر البوت )', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'الغاء', 'callback_data' => 'Cancel']]]])]);return false;
}
}else{
bot('sendmessage', ['chat_id' => $chatId, 'text' => 'عذرا الرساله ليست ب ايدي', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'الغاء', 'callback_data' => 'Cancel']]]])]);return false;
}
}
if($data == "csaddadmin"){
$status[$chatId] = "csaddadmin";
file_put_contents("settings.json",json_encode($status,128|32|256));
bot('editMessageText', ['chat_id' => $chatId, 'message_id' => $messageId, 'text' => 'يرجى ارسال ايدي الشخص ', 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'الغاء', 'callback_data' => 'Cancel']]]]) ]);
}
if($data == "Adminsh"){
ViewAdminsh($chatId,'edit',$messageId);
}
if (preg_match("/^(admindel)([+])(.*)/s", $data)){
preg_match("/^(admindel)([+])(.*)/s", $data, $matcha);
$n = $matcha[3];
unset($adminv['admin'][$n]);
$adminv['admin']= array_values($adminv['admin']); 
file_put_contents("Adminsh.json", json_encode($adminv,128|32|256));
ViewAdminsh($chatId,'edit',$messageId);
}
if($Ty=="addcf" and !$data){
if(!in_array($messageText,$media['name'])){
$media['name'][]=$messageText;
$media['type'][$chatId]=false;
Save($media);
bot('sendmessage', ['chat_id' => $chatId, 'text' =>"•  تم حفظ الرابط بنجاح", 'reply_markup' =>$keyboard2]);
}else{
bot('sendmessage', ['chat_id' => $chatId, 'text' =>"• الرابط موجود بالفعل"]);
}
}
if($data=="addcf"){
bot('EditMessageText', [
'chat_id' => $chatId,
'message_id' => $messageId,
'text' =>"قم بارسال الرابط الان",
'parse_mode' => "markdown",
'disable_web_page_preview' => true,
"reply_markup" =>$keyboard2,
]);
$media['type'][$chatId]=$data;
Save($media);
}
if($data=="Vcf"){
if(count($media['name'])!=0){
$reply_markup = [];
foreach($media['name'] as $T){
$reply_markup['inline_keyboard'][] =[['text'=>$T,'url'=>$T],['text'=>"🗑",'callback_data'=>"Def_".$T]];
}
$reply_markup['inline_keyboard'][] =[['text'=>"➕",'callback_data'=>"addcf"]];
$reply_markup['inline_keyboard'][] =[['text'=>"رجوع",'callback_data'=>"Cancel"]];
$K=json_encode($reply_markup); 
bot('EditMessageText', [
'chat_id' => $chatId,
'message_id' => $messageId,
'text' =>"• اليك الاشتراكات الوهميه",
'parse_mode' => "markdown",
'disable_web_page_preview' => true,
"reply_markup" =>$K
]);
}else{
bot('EditMessageText', [
'chat_id' => $chatId,
'message_id' => $messageId,
'text' =>"لم تقم بأضافه اي اشتراك وهمي",
'parse_mode' => "markdown",
'disable_web_page_preview' => true,
"reply_markup" =>$keyboard2
]);
}
}
}
if(preg_match("/(Def_)(.*?)/",$data)){
$st=str_replace("Def_","",$data);
$st=array_search($st,$media['name']);
unset($t[$media['name'][$st]]);
Sv("t.json",$t);
unset($media['name'][$st]);
$media['name']=array_values($media['name']);
Save($media);
$k="تم حذف الاشتراك";
$reply_markup = [];
foreach($media['name'] as $T){
if($T!=$st){
$reply_markup['inline_keyboard'][] =[['text'=>$T,'url'=>$T],['text'=>"🗑",'callback_data'=>"Def_".$T]];
}
}
$reply_markup['inline_keyboard'][] =[['text'=>"➕",'callback_data'=>"addcf"]];
$reply_markup['inline_keyboard'][] =[['text'=>"رجوع",'callback_data'=>"Cancel"]];
$K = json_encode($reply_markup); 
bot('EditMessageText', [
'chat_id' => $chatId,
'message_id' => $messageId,
'text' =>$k,
'parse_mode' => "markdown",
'disable_web_page_preview' => true,
"reply_markup" =>$K,
]);
}
if($data=="Dcf"){
if(count($media['name'])!=0){
bot('EditMessageText', [
'chat_id' => $chatId,
'message_id' => $messageId,
'text' =>"تم حذف الاشتراكات الوهميه",
'parse_mode' => "markdown",
'disable_web_page_preview' =>true,
"reply_markup" =>$keyboard2,
]);
for($i=0;$i<count($media['name']);$i++){
unset($t[$media['name'][$i]]);
Sv("t.json",$t);
}
unset($media['name']);
Save($media);
}else{
bot('EditMessageText', [
'chat_id' => $chatId,
'message_id' => $messageId,
'text' =>"لم تقم بأضافه اي اشتراك",
'parse_mode' => "markdown",
'disable_web_page_preview' =>true,
"reply_markup" =>$keyboard2,
]);
}
}
if ($data == "ChaneLf") {
bot('EditMessageText', [
'chat_id' => $chatId,
'message_id' => $messageId,
'text' =>"اهلا بك في قسم الاشتراك الوهمي",
'parse_mode' => "markdown",
'disable_web_page_preview' => true,
"reply_markup" =>json_encode([
'inline_keyboard'=>[
[['text'=>"اضافه اشتراك ➕",'callback_data'=>"addcf"]],
[['text'=>"عرض الاشتراكات 📋",'callback_data'=>"Vcf"],['text'=>"حذف الاشتراكات 🗑",'callback_data'=>"Dcf"]],
[['text'=>"رجوع",'callback_data'=>"Cancel"]],
]])
]);
}
if(!is_deved($from_id)){
if(!CheckChannels($from_id)){
return false;
}
}
if(!is_deved($from_id)){
if($media['name']!=null){
for($i=0;$i<count($media['name']);$i++){
if($t[$media['name'][$i]][$chatId]!=2){
bot('sendmessage',[
'chat_id'=>$chatId,
"text"=>"*▪️- عذراً قبل الاستخدام عليك اولاً 🧚🏿‍♀\nالاشتراك في القناه من خلال الرابط ⤵️*",
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>'- اضغط لدخول الى القناه .📎', 'url'=>$media['name'][$i]]],
]])
]);
$t[$media['name'][$i]][$chatId] = $t[$media['name'][$i]][$chatId]+1;
Sv("t.json",$t);
exit();
break;
}
}
}
}
if($messageText == "/start"){
bot('sendmessage',[
'chat_id'=>$chatId,
'text'=>"
*• مرحباً بك بوت الزخرفه المتميزه عزيزي $from_name 🌵.*

- يمكنك زخرفة اسمك بخطوط انقليزيه جميله و مميزه هنا وايضاً يمكنك اختيار من الاسماء الجاهزه
ٴ——————————————",
'parse_mode'=>"markdown",'disable_web_page_preview'=>true,
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[['text'=>'𝒂𝒓𝒂𝒃𝒊𝒄 ✯','callback_data'=>'ii'],['text'=>'𝒆𝒏𝒈𝒍𝒊𝒔𝒉 ✯','callback_data'=>'ww']],
[['text'=>'رموز 𝟏','callback_data'=>'v1'],['text'=>'رموز 𝟐','callback_data'=>'v2']],
[['text'=>'اسماء قنوات و قروبات 🎄','callback_data'=>'cgasm']],
[['text'=>'اسماء انقليزيه 🎅🏻','callback_data'=>'enasm'],['text'=>'اسماء عربيه 🎅🏻','callback_data'=>'arasm']],
[['text'=>'مواليد و شهور مزخرفه 🍩','callback_data'=>'dates']],
[['text'=>'𝒖𝒑𝒅𝒂𝒕𝒆𝒔' ,'url'=>"t.me/SSSSR_Y"]],
]
])
]);
}
if($data == "home" ){
bot('EditMessageText',[
'chat_id'=>$chatId,
'message_id'=>$messageId,
'text'=>"
*• مرحباً بك بوت الزخرفه المتميزه مجددا 🌵.*

- يمكنك زخرفة اسمك بخطوط انقليزيه جميله و مميزه هنا وايضاً يمكنك اختيار من الاسماء الجاهزه
ٴ——————————————
",
'parse_mode'=>"Markdown",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[['text'=>'𝒂𝒓𝒂𝒃𝒊𝒄 ✯','callback_data'=>'ii'],['text'=>'𝒆𝒏𝒈𝒍𝒊𝒔𝒉 ✯','callback_data'=>'ww']],
[['text'=>'رموز 𝟏','callback_data'=>'v1'],['text'=>'رموز 𝟐','callback_data'=>'v2']],
[['text'=>'اسماء قنوات و قروبات 🎄','callback_data'=>'cgasm']],
[['text'=>'اسماء انقليزيه 🎅🏻','callback_data'=>'enasm'],['text'=>'اسماء عربيه 🎅🏻','callback_data'=>'arasm']],
[['text'=>'مواليد و شهور مزخرفه 🍩','callback_data'=>'dates']],
[['text'=>'𝒖𝒑𝒅𝒂𝒕𝒆𝒔' ,'url'=>"t.me/SSSSR_Y"]],
]
])
]);
}
if($data == "v1" ){
bot('EditMessageText',[
'chat_id'=>$chatId,
'message_id'=>$messageId,
'text'=>'𓅄 𓅅 𓅆 𓅇 𓅈 𓅉 𓅊 𓅋 𓅌 𓅍 𓅎 𓅏 𓅐 𓅑 𓅒 𓅓 𓅔𓅕 𓅖 𓅗 𓅘 𓅙 𓅚 𓅛 𓅜 𓅝 𓅞 𓅟 𓅠 𓅡 𓅢 𓅣 𓅤 𓅥 𓅦 𓅧 𓅨 𓅩 𓅫 𓅬 𓅭 𓅮 𓅯 𓅰 𓅱 𓅲 𓅳 𓅴 
‏𓅵 𓅶 𓅷 𓅸 𓅹 𓅺 𓅻 
‏ ☤ 𓅾 𓅿 𓆀 𓆁 𓆂


‏𓀀 𓀁 𓀂 𓀃 𓀄 𓀅 𓀆 𓀇 𓀈 𓀉 𓀊 𓀋 𓀌 𓀍 𓀎 𓀏 𓀐 𓀑 𓀒 𓀓 𓀔 𓀕 𓀖 𓀗 𓀘 𓀙 𓀚 𓀛 𓀜 𓀝 𓀞 𓀟 𓀠 𓀡 𓀢 𓀣 𓀤 𓀥 𓀦 𓀧 𓀨 𓀩 𓀪 𓀫 𓀬 𓀭 𓀮 𓀯 𓀰 𓀱 𓀲 𓀳 𓀴 𓀵 𓀶 𓀷 𓀸 𓀹 𓀺 𓀻 𓀼 𓀽 𓀾 𓀿 𓁀 𓁁 𓁂 𓁃 𓁄 𓁅 𓁆 𓁇 𓁈 𓁉 𓁊 𓁋 𓁌 𓁍 𓁎 𓁏 𓁐 𓁑 𓁒 𓁓 𓁔 𓁕 𓁖 𓁗 𓁘 𓁙 𓁚 𓁛 𓁜 𓁝 𓁞 𓁟 𓁠 𓁡 𓁢 𓁣 𓁤 𓁥 𓁦 𓁧 𓁨 𓁩 𓁪 𓁫 𓁬 𓁭 𓁮 𓁯 𓁰 𓁱 𓁲 𓁳 𓁴 𓁵 𓁶 𓁷 𓁸 𓁹 𓁺 𓁻 𓁼 𓁽 𓁾 𓁿 𓂀𓂅 𓂆 𓂇 𓂈 𓂉 𓂊 𓂋 𓂌 𓂍 𓂎 𓂏 𓂐 𓂑 𓂒 𓂓 𓂔 𓂕 𓂖 𓂗 𓂘 𓂙 𓂚 𓂛 𓂜 𓂝 𓂞 𓂟 𓂠 𓂡 𓂢 𓂣 𓂤 𓂥 𓂦 𓂧 𓂨 𓂩 𓂪 𓂫 𓂬 𓂭 𓂮 𓂯 𓂰 𓂱 𓂲 𓂳 𓂴 𓂵 𓂶 𓂷 𓂸 𓂹 𓂺 𓂻 𓂼 𓂽 𓂾 𓂿 𓃀 𓃁 𓃂 𓃃 𓃅 𓃆 𓃇 𓃈 𓃉 𓃊 𓃋 𓃌 𓃍 𓃎 𓃏 𓃐 𓃑 𓃒 𓃓 𓃔 𓃕 𓃖 𓃗 𓃘 𓃙 𓃚 𓃛 𓃜 𓃝 𓃞 𓃟 𓃠 𓃡 𓃢 𓃣 𓃤 𓃥 𓃦 𓃧 𓃨 𓃩 𓃪 𓃫 𓃬 𓃭 𓃮 𓃯 𓃰 𓃱 𓃲 𓃳 𓃴 𓃵 𓃶 𓃷 𓃸 𓃹 𓃺 𓃻 𓃼 𓃽 𓃾 𓃿 𓄀 𓄁 𓄂 𓄃 𓄄 𓄅 𓄆 𓄇 𓄈 𓄉 𓄊 𓄋 𓄌 𓄍 𓄎 𓄏 𓄐 𓄑 𓄒 𓄓 𓄔 𓄕 𓄖 𓄙 𓄚 𓄛 𓄜 𓄝 𓄞 𓄟 𓄠 𓄡 𓄢 𓄣 𓄤 𓄥 𓄦 𓄧 𓄨 𓄩 𓄪 𓄫 𓄬 𓄭 𓄮 𓄯 𓄰 𓄱 𓄲 𓄳 𓄴 𓄵 𓄶 𓄷 𓄸 𓄹 𓄺   𓄼 𓄽 𓄾 𓄿 𓅀 𓅁 𓅂 𓅃 𓅄 𓅅 𓅆 𓅇 𓅈 𓅉 𓅊 𓅋 𓅌 𓅍 𓅎 𓅏 𓅐 𓅑 𓅒 𓅓 𓅔 𓅕 𓅖 𓅗 𓅘 𓅙 𓅚 𓅛 𓅜 𓅝 𓅞 𓅟 𓅠 𓅡 𓅢 𓅣 𓅤 𓅥 𓅦 𓅧 𓅨 𓅩 𓅪 𓅫 𓅬 𓅭 𓅮 𓅯 𓅰 𓅱 𓅲 𓅳 𓅴 𓅵 𓅶 𓅷 𓅸 𓅹 𓅺 𓅻 𓅼 𓅽 𓅾 𓅿 𓆀 𓆁 𓆂 𓆃 𓆄 𓆅 𓆆 𓆇 𓆈 𓆉 𓆊 𓆋 𓆌 𓆍 𓆎 𓆐 𓆑 𓆒 𓆓 𓆔 𓆕 𓆖 𓆗 𓆘 𓆙 𓆚 𓆛 𓆜 𓆝 𓆞 𓆟 𓆠 𓆡 𓆢 𓆣 𓆤 𓆥 𓆦 𓆧 𓆨 𓆩𓆪 𓆫 𓆬 𓆭 𓆮 𓆯 𓆰 𓆱 𓆲 𓆳 𓆴 𓆵 𓆶 𓆷 𓆸 𓆹 𓆺 𓆻 𓆼 𓆽 𓆾 𓆿 𓇀 𓇁 𓇂 𓇃 𓇄 𓇅 𓇆 𓇇 𓇈 𓇉 𓇊 𓇋 𓇌 𓇍 𓇎 𓇏 𓇐 𓇑 𓇒 𓇓 𓇔 𓇕 𓇖 𓇗 𓇘 𓇙 𓇚 𓇛 𓇜 𓇝 𓇞 𓇟 𓇠 𓇡 𓇢 𓇣 𓇤 𓇥 𓇦 𓇧 𓇨 𓇩 𓇪 𓇫 𓇬 𓇭 𓇮 𓇯 𓇰 𓇱 𓇲 𓇳 𓇴 𓇵 𓇶 𓇷 𓇸 𓇹 𓇺 𓇻 𓇼 𓇾 𓇿 𓈀 𓈁 𓈂 𓈃 𓈄 𓈅 𓈆 𓈇 𓈈 𓈉 𓈊 𓈋 𓈌 𓈍 𓈎 𓈏 𓈐 𓈑 𓈒 𓈓 𓈔 𓈕 𓈖 𓈗 𓈘 𓈙 𓈚 𓈛 𓈜 𓈝 𓈞 𓈟 𓈠 𓈡 𓈢 𓈣 𓈤  𓈥 𓈦 𓈧 𓈨 𓈩 𓈪 𓈫 𓈬 𓈭 𓈮 𓈯 𓈰 𓈱 𓈲 𓈳 𓈴 𓈵 𓈶 𓈷 𓈸 𓈹 𓈺 𓈻 𓈼 𓈽 𓈾 𓈿 𓉀 𓉁 𓉂 𓉃 𓉄 𓉅 𓉆 𓉇 𓉈 𓉉 𓉊 𓉋 𓉌 𓉍 𓉎 𓉏 𓉐 𓉑 𓉒 𓉓 𓉔 𓉕 𓉖 𓉗 𓉘 𓉙 𓉚 𓉛 𓉜 𓉝 𓉞 𓉟 𓉠 𓉡 𓉢 𓉣 𓉤 𓉥 𓉦 𓉧 𓉨 𓉩 𓉪 𓉫 𓉬 𓉭 𓉮 𓉯 𓉰 𓉱 𓉲 𓉳 𓉴 𓉵 𓉶 𓉷 𓉸 𓉹 𓉺 𓉻 𓉼 𓉽 𓉾 𓉿 𓊀 𓊁 𓊂 𓊃 𓊄 𓊅 𓊈 𓊉 𓊊 𓊋 𓊌 𓊍 𓊎 𓊏 𓊐 𓊑 𓊒 ?? 𓊔 𓊕 ?? ?? 𓊘 𓊙 𓊚 𓊛 𓊜 𓊝 𓊞 𓊟 𓊠 𓊡 𓊢 𓊣 𓊤 𓊥 𓊦 𓊧 𓊨 𓊩 𓊪 𓊫 𓊬 𓊭 𓊮 𓊯 ?? 𓊱 𓊲 𓊳 𓊴 𓊵 𓊶 𓊷 𓊸 𓊹 𓊺 𓊻 𓊼 ?? ?? 𓊿 𓋀 𓋁 𓋂 𓋃 𓋄 𓋅 𓋆 𓋇 𓋈 𓋉 𓋊 𓋋 𓋌 𓋍 𓋎 𓋏 𓋐 𓋑 𓋒 𓋓 𓋔 𓋕 𓋖 𓋗 𓋘 𓋙 𓋚 𓋛 𓋜 𓋝 𓋞 𓋟 𓋠 𓋡 𓋢 𓋣 𓋤 𓋥 𓋦 𓋧 𓋨 𓋩 𓋪 𓋫 𓋬 𓋭 𓋮 𓋯 𓋰 𓋱 𓋲 𓋳 𓋴 𓋵 𓋶 𓋷 𓋸 𓋹 𓋺 𓋻 𓋼 𓋽 𓋾 𓋿 𓌀 𓌁 𓌂 𓌃 𓌄 𓌅 𓌆 𓌇 𓌈 𓌉 𓌊 𓌋 𓌌 𓌍 𓌎 𓌏 𓌐 𓌑 𓌒 𓌓 𓌔 𓌕 𓌖 𓌗 𓌘 𓌙 𓌚 𓌛 𓌜 𓌝 𓌞 𓌟 𓌠 𓌡 𓌢 𓌣 𓌤 𓌥 𓌦 𓌧 𓌨 𓌩 𓌪 𓌫 𓌬 𓌭 𓌮 𓌯 𓌰 𓌱 𓌲 𓌳 𓌴 𓌵 𓌶 𓌷 𓌸 𓌹 𓌺 𓌻 𓌼 𓌽 𓌾 𓌿 𓍀 𓍁 𓍂 𓍃 𓍄 𓍅 𓍆 𓍇 𓍈 𓍉 𓍊 𓍋 𓍌 𓍍 𓍎 𓍏 𓍐 𓍑 𓍒 𓍓 𓍔 𓍕 𓍖 𓍗 𓍘 𓍙 𓍚 𓍛 𓍜 𓍝 𓍞 𓍟 𓍠 𓍡 𓍢 𓍣 𓍤 𓍥 𓍦 𓍧 𓍨 𓍩 𓍪 𓍫 𓍬 𓍭 𓍮 𓍯 𓍰 𓍱 𓍲 𓍳 𓍴 𓍵 𓍶 𓍷 𓍸 𓍹 𓍺 𓍻 𓍼 𓍽 𓍾 𓍿 𓎀 𓎁 𓎂 𓎃 𓎄 𓎅 𓎆 𓎇 𓎈 𓎉 𓎊 𓎋 𓎌 𓎍 𓎎 𓎏 𓎐 𓎑 𓎒 𓎓 𓎔 𓎕 𓎖 𓎗 𓎘 𓎙 𓎚 𓎛 𓎜 𓎝 𓎞 𓎟 𓎠 𓎡 𓏋 𓏌 𓏍 𓏎 𓏏 𓏐 𓏑 𓏒 𓏓 
‏ 𓏕 𓏖 𓏗 𓏘 𓏙 𓏚 𓏛 𓏜 𓏝 𓏞 𓏟 𓏠 𓏡 𓏢 𓏣 𓏤 𓏥 𓏦 𓏧 𓏨 𓏩 𓏪 𓏫 𓏬 𓏭 𓏮 𓏯 𓏰 𓏱 𓏲 𓏳 𓏴 𓏶 𓏷 𓏸 𓏹 𓏺 𓏻 𓏼 𓏽 𓏾 𓏿 𓐀 𓐁 𓐂 𓐃 𓐄 𓐅 𓐆
- 𖣨 ، ෴ ، 𖡺  ، 𖣐 ، ✜ ، ✘ ، 𖡻 ،
- ༄ ، ༺༻ ، ༽༼ ،  ╰☆╮،  
- ɵ̷᷄ˬɵ̷᷅ ، ‏⠉̮⃝ ، ࿇࿆ ، ꔚ، ま ، ☓ ،
{𓆉 . 𓃠 .𓅿 . 𓃠 . 𓃒 . 𓅰 . 𓃱 . 𓅓 . 𐂃  . ꕥ  . ⌘ . ♾ .    ꙰  .  . ᤑ .  ﾂ .
____
✦ ,✫ ,✯, ✮ ,✭ ,✰, ✬ ,✧, ✤, ❅ , 𒀭,✵ , ✶ , ✷ , ✸ , ✹ ,⧫, . 𐂂 }

-〘 𖢐 ، 𒍦 ، 𒍧 ، 𖢣 ، 𝁫 ، 𒍭 ، 𝁅 ، 𝁴 ، 𒍮 ، 𝁵 ، 𝀄 ، 𓏶 ، ?? ، 𓏷 ، 𓏯 ، 𓏴 ، 𓏳 ، 𓏬 ، 𓏦 ، 𓏵 ، 𓏱 ، ᳱ ، ᯼ ، 𐃕 ، ᯥ ، ᯤ ، ᯾ ، ᳶ ، ᯌ ، ᢆ ،

ᥦ ، ᨙ ، ᨚ  ، ᨔ  ، ⏢ ، ⍨ ، ⍃ ، ⏃ ، ⍦ ، ⏕ ، ⏤ ، ⏁ ، ⏂ ، ⏆ ، ⌳ ، ࿅ ، ࿕ ، ࿇ ، ᚙ ، ࿊ ، ࿈ ، ྿ ،
࿂ ، ࿑ ،  ᛥ ، ࿄ ، 𐀁 ، 𐀪 ، 𐀔 ، 𐀴 ، 𐀤 ، 𐀦 ، 𐀂 ، 𐀣 ، 𐀢 ، 𐀶 ، 𐀷 ، 𐂭 ، 𐂦 ، 𐂐 ، 𐂅 ، 𐂡 ، 𐂢 ، 𐂠 ، 𐂓 ، 𐂑 ، 𐃸 ، 𐃶 ، 𐂴 ، 𐃭 ، 𐃳 ، 𐃣 ، 𐂰 ، 𐃟 ، 𐃐 ، 𐃙 ، 𐃀 ، 𐇮 ، 𐇹 ، 𐇲 ، 𐇩 ، 𐇪 ، 𐇶 ، 𐇻 ، 𐇡 ، 𐇸 ، 𐇣 ، 𐇤 ، 𐎅 ، 𐏍 ، 𐎃 ، 𐏒 ، 𐎄 ، 𐏕 〙.


╔ ╗. 𓌹  𓌺 .〝  〞. ‹ ›  .「  」. ‌‏𓂄‏ ‌‌‏𓂁
〖 〗. 《》 .  < > . « »  . ﹄﹃

₁ ₂ ₃ ₄ ₅ ₆ ₇ ₈ ₉ ₀
𝟏 𝟐 𝟑 𝟒 𝟓 𝟔 𝟕 𝟖 𝟗 𝟎
𝟭 𝟮 𝟯 𝟰 𝟱 𝟲 𝟳 𝟴 𝟵 𝟬
①②③④⑤⑥⑦⑧⑨⓪
❶❷❸❹❺❻❼❽❾⓿
⓫⓬⓭⓮⓯⓰⓱⓲⓳⓴
——————————————
𝟶 𝟷 𝟸 𝟹 𝟺 𝟻 𝟼 𝟽 𝟾  𝟿
𝟘 𝟙  𝟚  𝟛  𝟜  𝟝  𝟞  𝟟  𝟠 𝟡
𝟬 𝟭  𝟮  𝟯  𝟰  𝟱   𝟲  𝟳  𝟴  𝟵  
𝟎  𝟏  𝟐  𝟑  𝟒   𝟓   𝟔  𝟕   𝟖   𝟗
０ １ ２ ３ ４ ５ ６ ７８９
——————————————
.....
.',
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[['text'=>'رجوع' ,'callback_data'=>"home"]],
]])
]);   
}
if($data == "v2" ){
bot('EditMessageText',[
'chat_id'=>$chatId,
'message_id'=>$messageId,
'text'=>'
ᾋ ᾌ ᾍ ᾎ ᾏ ᾐ ᾑ ᾒ ᾓ ᾔ ᾕ ᾖ ᾗ ᾘ ᾙ ᾚ ᾛ ᾜ ᾝ ᾞ ᾟ ᾠ ᾡ ᾢ ᾣ ᾤ ᾥ ᾦ ᾧ ᾨ ᾩ ᾪ ᾫ ᾬ ᾭ ᾮ ᾯ ᾰ ᾱ ᾲ ᾳ ᾴ ᾶ ᾷ Ᾰ Ᾱ Ὰ Ά ᾼ ᾽ ι ᾿ ῀ ῁ ῂ ῃ ῄ ῆ ῇ Ὲ Έ Ὴ Ή ῌ ῍ ῎ ῏ ῐ ῑ ῒ ΐ ῖ ῗ Ῐ Ῑ Ὶ Ί ῝ ῞ ῟ ῠ ῡ ῢ ΰ ῤ ῥ ῦ ῧ Ῠ Ῡ Ὺ Ύ Ῥ ῭ ΅ ` ῲ ῳ ῴ ῶ ῷ Ὸ Ό Ὼ Ώ ῼ ´ ῾ ῿                       ​ ‌ ‍ ‎ ‏ ‐ ‑ ‒ – — ― ‖ ‗ ‘ ’ ‚ ‛ “ ” „ ‟ † ‡ • ‣ ․ ‥ … ‧       ‰ ‱ ′ ″ ‴ ‵ ‶ ‷ ‸ ‹ › ※ ‼ ‽ ‾ ‿ ⁀ ⁁ ⁂ ⁃ ⁄ ⁅ ⁆ ⁇ ⁈ ⁉ ⁊ ⁋ ⁌ ⁍ ⁎ ⁏ ⁐ ⁑ ⁒ ⁓ ⁔ ⁕ ⁖ ⁗ ⁘ ⁙ ⁚ ⁛ ⁜ ⁝ ⁞   ⁠ ⁡ ⁢ ⁣ ⁤ ⁥ ⁦ ⁧ ⁨ ⁩ ⁪ ⁫ ⁬ ⁭ ⁮ ⁯ ⁰ ⁱ ⁴ ⁵ ⁶ ⁷ ⁸ ⁹ ⁿ ₀ ₁ ₂ ₃ ₄ ₅ ₆ ₇ ₈ ₉ ₐ ₑ ₒ ₓ ₕ ₖ ₗ ₘ ₙ ₚ ₛ ₜ ₝ ₞ ₟ ₠ ₡ ₢ ₣ ₤ ₥ ₦ ₧ ₨ ₩ ₪ ₫ € ₭ ₮ ₯ ₰ ₱ ₲ ₳ ₴ ₵ ℀ ℁ ℂ ℃ ℄ ℅ ℆ ℇ ℈ ℉ ℊ ℋ ℌ ℍ ℎ ℏ ℐ ℑ ℒ ℓ ℔ ℕ № ℗ ℘ ℙ ℚ ℛ ℜ ℝ ℞ ℟ ℠ ℡ ™ ℣ ℤ ℥ Ω ℧ ℨ ℩ K Å ℬ ℭ ℮ ℯ ℰ ℱ Ⅎ ℳ ℴ ℵ ℶ ℷ ℸ ℹ ℺ ℻ ℼ ℽ ℾ ℿ ⅀ ⅁ ⅂ ⅃ ⅄ ⅅ ⅆ ⅇ ⅈ ⅉ ⅊ ⅋ ⅌ ⅍ ⅎ ⅏ ⅐ ⅑ ⅒ ⅓ ⅔ ⅕ ⅖ ⅗ ⅘ ⅙ ⅚ ⅛ ⅜ ⅝ ⅞ ↀ ↁ ↂ Ↄ ↉ ↊ ↋ ← ↑ → ↓ ↔ ↕ ↖ ↗ ↘ ↙ ↚ ↛ ↜ ↝ ↞ ↟ ↠ ↡ ↢ ↣ ↤ ↥ ↦ ↧ ↨ ↩ ↪ ↫ ↬ ↭ ↮ ↯ ↰ ↱ ↲ ↳ ↴ ↵ ↶ ↷ ↸ ↹ ↺ ↻ ↼ ↽ ↾ ↿ ⇀ ⇁ ⇂ ⇃ ⇄ ⇅ ⇆ ⇇ ⇈ ⇉ ⇊ ⇋ ⇌ ⇍ ⇎ ⇏ ⇐ ⇑ ⇒ ⇓ ⇔ ⇕ ⇖ ⇗ ⇘ ⇙ ⇚ ⇛ ⇜ ⇝ ⇞ ⇟ ⇠ ⇡ ⇢ ⇣ ⇤ ⇥ ⇦ ⇧ ⇨ ⇩ ⇪ ⇫ ⇬ ⇭ ⇮ ⇯ ⇰ ⇱ ⇲ ⇳ ⇴ ⇵ ⇶ ⇷ ⇸ ⇹ ⇺ ⇻ ⇼ ⇽ ⇾ ⇿ ∀ ∁ ∂ ∃ ∄ ∅ ∆ ∇ ∈ ∉ ∊ ∋ ∌ ∍ ∎ ∏ ∐ ∑ − ∓ ∔ ∕ ∖ ∗ ∘ ∙ √ ∛ ∜ ∝ ∞ ∟ ∠ ∡ ∢ ∣ ∤ ∥ ∦ ∧ ∨ ∩ ∪ ∫ ∬ ∭ ∮ ∯ ∰ ∱ ∲ ∳ ∴ ∵ ∶ ∷ ∸ ∹ ∺ ∻ ∼ ∽ ∾ ∿ ≀ ≁ ≂ ≃ ≄ ≅ ≆ ≇ ≈ ≉ ≊ ≋ ≌ ≍ ≎ ≏ ≐ ≑ ≒ ≓ ≔ ≕ ≖ ≗ ≘ ≙ ≚ ≛ ≜ ≝ ≞ ≟ ≠ ≡ ≢ ≣ ≤ ≥ ≦ ≧ ≨ ≩ ≪ ≫ ≬ ≭ ≮ ≯ ≰ ≱ ≲ ≳ ≴ ≵ ≶ ≷ ≸ ≹ ≺ ≻ ≼ ≽ ≾ ≿ ⊀ ⊁ ⊂ ⊃ ⊄ ⊅ ⊆ ⊇ ⊈ ⊉ ⊊ ⊋ ⊌ ⊍ ⊎ ⊏ ⊐ ⊑ ⊒ ⊓ ⊔ ⊕ ⊖ ⊗ ⊘ ⊙ ⊚ ⊛ ⊜ ⊝ ⊞ ⊟ ⊠ ⊡ ⊢ ⊣ ⊤ ⊥ ⊦ ⊧ ⊨ ⊩ ⊪ ⊫ ⊬ ⊭ ⊮ ⊯ ⊰ ⊱ ⊲ ⊳ ⊴ ⊵ ⊶ ⊷ ⊸ ⊹ ⊺ ⊻ ⊼ ⊽ ⊾ ⊿ ⋀ ⋁ ⋂ ⋃ ⋄ ⋅ ⋆ ⋇ ⋈ ⋉ ⋊ ⋋ ⋌ ⋍ ⋎ ⋏ ⋐ ⋑ ⋒ ⋓ ⋔ ⋕ ⋖ ⋗ ⋘ ⋙ ⋚ ⋛ ⋜ ⋝ ⋞ ⋟ ⋠ ⋡ ⋢ ⋣ ⋤ ⋥ ⋦ ⋧ ⋨ ⋩ ⋪ ⋫ ⋬ ⋭ ⋮ ⋯ ⋰ ⋱ ⋲ ⋳ ⋴ ⋵ ⋶ ⋷ ⋸ ⋹ ⋺ ⋻ ⋼ ⋽ ⋾ ⋿ ⌀ ⌁ ⌂ ⌃ ⌄ ⌅ ⌆ ⌇ ⌈ ⌉ ⌊ ⌋ ⌌ ⌍ ⌎ ⌏ ⌐ ⌑ ⌒ ⌓ ⌔ ⌕ ⌖ ⌗ ⌘ ⌙ ⌚ ⌛ ⌜ ⌝ ⌞ ⌟ ⌠ ⌡ ⌢ ⌣ ⌤ ⌥ ⌦ ⌧ ⌨ 〈 〉 ⌫ ⌬ ⌭ ⌮ ⌯ ⌰ ⌱ ⌲ ⌳ ⌴ ⌵ ⌶ ⌷ ⌸ ⌹ ⌺ ⌻ ⌼ ⌽ ⌾ ⌿ ⍀ ⍁ ⍂ ⍃ ⍄ ⍅ ⍆ ⍇ ⍈ ⍉ ⍊ ⍋ ⍌ ⍍ ⍎ ⍏ ⍐ ⍑ ⍒ ⍓ ⍔ ⍕ ⍖ ⍗ ⍘ ⍙ ⍚ ⍛ ⍜ ⍝ ⍞ ⍟ ⍠ ⍡ ⍢ ⍣ ⍤ ⍥ ⍦ ⍧ ⍨ ⍩ ⍪ ⍫ ⍬ ⍭ ⍮ ⍯ ⍰ ⍱ ⍲ ⍳ ⍴ ⍵ ⍶ ⍷ ⍸ ⍹ ⍺ ⍻ ⍼ ⍽ ⍾ ⍿ ⎀ ⎁ ⎂ ⎃ ⎄ ⎅ ⎆ ⎇ ⎈ ⎉ ⎊ ⎋ ⎌ ⎍ ⎎ ⎏ ⎐ ⎑ ⎒ ⎓ ⎔ ⎕ ⎖ ⎗ ⎘ ⎙ ⎚ ⎛ ⎜ ⎝ ⎞ ⎟ ⎠ ⎡ ⎢ ⎣ ⎤ ⎥ ⎦ ⎧ ⎨ ⎩ ⎪ ⎫ ⎬ ⎭ ⎮ ⎯ ⎰ ⎱ ⎲ ⎳ ⎴ ⎵ ⎶ ⎷ ⎸ ⎹ ⎺ ⎻ ⎼ ⎽ ⎾ ⎿ ⏀ ⏁ ⏂ ⏃ ⏄ ⏅ ⏆ ⏇ ⏈ ⏉ ⏋ ⏌ ⏍ ⏎ ⏏ ⏐ ⏑ ⏒ ⏓ ⏔ ⏕ ⏖ ⏗ ⏘ ⏙ ⏚ ⏛ ⏜ ⏝ ⏞ ⏟ ⏠ ⏡ ⏢ ⏣ ⏤ ⏥ ⏦ ␋ ␢ ␣ ① ② ③ ④ ⑤ ⑥ ⑦ ⑧ ⑨ ⑩ ⑪ ⑫ ⑬ ⑭ ⑮ ⑯ ⑰ ⑱ ⑲ ⑳ ⑴ ⑵ ⑶ ⑷ ⑸ ⑹ ⑺ ⑻ ⑼ ⑽ ⑾ ⑿ ⒀ ⒁ ⒂ ⒃ ⒄ ⒅ ⒆ ⒇ ⒈ ⒉ ⒊ ⒋ ⒌ ⒍ ⒎ ⒏ ⒐ ⒑ ⒒ ⒓ ⒔ ⒕ ⒖ ⒗ ⒘ ⒙ ⒚ ⒛ ⒜ ⒝ ⒞ ⒟ ⒠ ⒡ ⒢ ⒣ ⒤ ⒥ ⒦ ⒧ ⒨ ⒩ ⒪ ⒫ ⒬ ⒭ ⒮ ⒯ ⒰ ⒱ ⒲ ⒳ ⒴ ⒵ Ⓐ Ⓑ Ⓒ Ⓓ Ⓔ Ⓕ Ⓖ Ⓗ Ⓘ Ⓙ Ⓚ Ⓛ Ⓜ Ⓝ Ⓞ Ⓟ Ⓠ Ⓡ Ⓢ Ⓣ Ⓤ Ⓥ Ⓦ Ⓧ Ⓨ Ⓩ ⓐ ⓑ ⓒ ⓓ ⓔ ⓕ ⓖ ⓗ ⓘ ⓙ ⓚ ⓛ ⓜ ⓝ ⓞ ⓟ ⓠ ⓡ ⓢ ⓣ ⓤ ⓥ ⓦ ⓧ ⓨ ⓩ ⓪ ⓫ ⓬ ⓭ ⓮ ⓯ ⓰ ⓱ ⓲ ⓳ ⓴ ⓵ ⓶ ⓷ ⓸ ⓹ ⓺ ⓻ ⓼ ⓽ ⓾ ⓿ ─ ━ │ ┃ ┄ ┅ ┆ ┇ ┈ ┉ ┊ ┋ ┌ ┍ ┎ ┏ ┐ ┑ ┒ ┓ └ ┕ ┖ ┗ ┘ ┙ ┚ ┛ ├ ┝ ┞ ┟ ┠ ┡ ┢ ┣ ┤ ┥ ┦ ┧ ┨ ┩ ┪ ┫ ┬ ┭ ┮ ┯ ┰ ┱ ┲ ┳ ┴ ┵ ┶ ┷ ┸ ┹ ┺ ┻ ┼ ┽ ┾ ┿ ╀ ╁ ╂ ╃ ╄ ╅ ╆ ╇ ╈ ╉ ╊ ╋ ╌ ╍ ╎ ╏ ═ ║ ╒ ╓ ╔ ╕ ╖ ╗ ╘ ╙ ╚ ╛ ╜ ╝ ╞ ╟ ╠ ╡ ╢ ╣ ╤ ╥ ╦ ╧ ╨ ╩ ╪ ╫ ╬ ╬﹌ ╭ ╮ ╯ ╰ ╰☆╮ ╱ ╲ ╳ ╴ ╵ ╶ ╷ ╸ ╹ ╺ ╻ ╼ ╽ ╾ ╿ ▀ ▁ ▂ ▃ ▄ ▅ ▆ ▇ █ ▉ ▊ ▋ ▌ ▍ ▎ ▏ ▐ ░ ▒ ▓ ▔ ▕ ▖ ▗ ▘ ▙ ▚ ▛ ▜ ▝ ▞ ▟ ■ □ ▢ ▣ ▤ ▥ ▦ ▧ ▨ ▩ ▪ ▫ ▬ ▭ ▮ ▯ ▰ ▱ ▲ △ ▴ ▵ ▷ ▸ ▹ ► ▻ ▼ ▽ ▾ ▿  ◁ ◂ ◃ ◄ ◅ ◆ ◇ ◈ ◉ ◊ ○ ◌ ◍ ◎ ● ◐ ◑ ◒ ◓ ◔ ◔ʊ ◕ ◖ ◗ ◘ ◙ ◚ ◛ ◜ ◝ ◞ ◟ ◠ ◡ ◢ ◣ ◤ ◥ ◦ ◧ ◨ ◩ ◪ ◫ ◬ ◭ ◮ ◯ ◰ ◱ ◲ ◳ ◴ ◵ ◶ ◷ ◸ ◹ ◺  ☓☠ ☡☰ ☱ ☲ ☳ ☴ ☵ ☶ ☷ ♔ ♕ ♖ ♗ ♘ ♙ ♚ ♛ ♜ ♝ ♞ ♟ ♠ ♡ ♢  ♩ ♪ ♫ ♬ ♭ ♮ ♯ ♰ ♱ ♻ ♼ ♽ ⚆ ⚇ ⚈ ⚉ ⚊ ⚋ ⚌ ⚍ ⚎ ⚏ ⚐ ⚑ ✐ ✑ ✒ ✓ ✔ ✕ ✖ ✗ ✘ ✙ ✚ ✛ ✜  ✞ ✟ ✠ ✢ ✣ ✤ ✥ ✦ ✧ ✧♱ ✩ ✪ ✫ ✬ ✭ ✮ ✯ ✰ ✱ ✲  ✵ ✶ ✷ ✸ ✹ ✺ ✻ ✼ ✽ ✾ ✿ ❀ ❁ ❂ ❃ ❄ ❅ ❆ ❈ ❉ ❊ ❋ ❍ ❏ ❐ ❑ ❒ ❖ ❗ ❘ ❙ ❚ ❛ ❜ ❝ ❞ ❡ ❢ ❣ ❤ ❥ ❦ ❧ ❨ ❩ ❪ ❫ ❬ ❭ ❮ ❯ ❰ ❱ ❲ ❳ ❴ ❵ ❶ ❷ ❸ ❹ ❺ ❻ ❼ ❽ ❾ ❿ ➀ ➁ ➂ ➃ ➄ ➅ ➆ ➇ ➈ ➉ ➊ ➋ ➌ ➍ ➎ ➏ ➐➑ ➒ ➓ ➔ ➘ ➙ ➚ ➛ ➜ ➝ ➞ ➟ ➠  ➢ ➣ ➤ ➥ ➦ ➧ ➨ ➩ ➪ ➫ ➬ ➭ ➮ ➯ ➱ ➲ ➳ ➴ ➵ ➶ ➷ ➸ ➹ ➺ ➻ ➼ ➽ ➾ ⟀ ⟁ ⟂ ⟃ ⟄ ⟇ ⟈ ⟉ ⟊ ⟐ ⟑ ⟒ ⟓ ⟔ ⟕ ⟖ ⟗ ⟘ ⟙ ⟚ ⟛ ⟜ ⟝ ⟞ ⟟ ⟠ ⟡ ⟢ ⟣ ⟤ ⟥ ⟦ ⟧ ⟨ ⟩ ⟪ ⟫ ⟰ ⟱ ⟲ ⟳ ⟴ ⟵ ⟶ ⟷ ⟸ ⟹ ⟺ ⟻ ⟼ ⟽ ⟾ ⟿ ⤀ ⤁ ⤂ ⤃ ⤄ ⤅ ⤆ ⤇ ⤈ ⤉ ⤊ ⤋ ⤌ ⤍ ⤎ ⤏ ⤐ ⤑ ⤒ ⤓ ⤔ ⤕ ⤖ ⤗ ⤘ ⤙ ⤚ ⤛ ⤜ ⤝ ⤞ ⤟ ⤠ ⤡ ⤢ ⤣ ⤤ ⤥ ⤦ ⤧ ⤨ ⤩ ⤪ ⤫ ⤬ ⤭ ⤮ ⤯ ⤰ ⤱ ⤲ ⤳ ⤶ ⤷ ⤸ ⤹ ⤺ ⤻ ⤼ ⤽ ⤾ ⤿ ⥀ ⥁ ⥂ ⥃ ⥄ ⥅ ⥆ ⥇ ⥈ ⥉ ⥊ ⥋ ⥌ ⥍ ⥎ ⥏ ⥐ ⥑ ⥒ ⥓ ⥔ ⥕ ⥖ ⥗ ⥘ ⥙ ⥚ ⥛ ⥜ ⥝ ⥞ ⥟ ⥠ ⥡ ⥢ ⥣ ⥤ ⥥ ⥦ ⥧ ⥨ ⥩ ⥪ ⥫ ⥬ ⥭ ⥮ ⥯ ⥰ ⥱ ⥲ ⥳ ⥴ ⥵ ⥶ ⥷ ⥸ ⥹ ⥺ ⥻ ⥼ ⥽ ⥾ ⥿ ⦀ ⦁ ⦂ ⦃ ⦄ ⦅ ⦆ ⦇ ⦈ ⦉ ⦊ ⦋ ⦌ ⦍ ⦎ ⦏ ⦐ ⦑ ⦒ ⦓ ⦔ ⦕ ⦖ ⦗ ⦘ ⦙ ⦚ ⦛ ⦜ ⦝ ⦞ ⦟ ⦠ ⦡ ⦢ ⦣ ⦤ ⦥ ⦦ ⦧ ⦨ ⦩ ⦪ ⦫ ⦬ ⦭ ⦮ ⦯ ⦰ ⦱ ⦲ ⦳ ⦴ ⦵ ⦶ ⦷ ⦸ ⦹ ⦺ ⦻ ⦼ ⦽ ⦾ ⦿ ⧀ ⧁ ⧂ ⧃ ⧄ ⧅ ⧆ ⧇ ⧈ ⧉ ⧊ ⧋ ⧌ ⧍ ⧎ ⧏ ⧐ ⧑ ⧒ ⧓ ⧔ ⧕ ⧖ ⧗ ⧘ ⧙ ⧚ ⧛ ⧜ ⧝ ⧞ ⧟ ⧡ ⧢ ⧣ ⧤ ⧥ ⧦ ⧧ ⧨ ⧩ ⧪ ⧫ ⧬ ⧭ ⧮ ⧯ ⧰ ⧱ ⧲ ⧳ ⧴ ⧵ ⧶ ⧷ ⧸ ⧹ ⧺ɷ
——————————————
.
',
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[['text'=>'رجوع' ,'callback_data'=>"home"]],
]])
]);   
}
$using = json_decode(file_get_contents("using.json"),true);
if($data == "ii" ){
$using[$chatId] = "Arabic";
file_put_contents("using.json",json_encode($using,128|32|256));
bot('EditMessageText',[
'chat_id'=>$chatId,
'message_id'=>$messageId,
'text'=>'• حسنا قم بأرسال اسمك بلغة العربية فقط .',
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[['text'=>'رجوع' ,'callback_data'=>"home"]],
]])
]);   
}
if($data == "ww" ){
$using[$chatId] = "English";
file_put_contents("using.json",json_encode($using,128|32|256));
bot('EditMessageText',[
'chat_id'=>$chatId,
'message_id'=>$messageId,
'text'=>'- حسنا قم بأرسال اسمك بالغة الانقليزية فقط .',
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[['text'=>'رجوع' ,'callback_data'=>"home"]],
]])
]);   
}
$hmd = json_decode(file_get_contents("using.json"),true)[$chatId];
if(preg_match('/([a-z])|([A-Z])/i',$messageText)){
	if($hmd == 'English'){
      bot('sendmessage',[
  'chat_id'=>$chatId,
  'text'=>"",
  ]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('a','𝚊',$messageText); 
$marcus = str_replace('b','𝚋',$marcus); 
$marcus = str_replace('c','𝚌',$marcus); 
$marcus = str_replace('d','𝚍',$marcus); 
$marcus = str_replace('e','𝚎',$marcus); 
$marcus = str_replace('f','𝚏',$marcus); 
$marcus = str_replace('g','𝚐',$marcus); 
$marcus = str_replace('h','𝚑',$marcus); 
$marcus = str_replace('i','𝚒',$marcus); 
$marcus = str_replace('j','𝚓',$marcus); 
$marcus = str_replace('k','𝚔',$marcus); 
$marcus = str_replace('l','𝚕',$marcus); 
$marcus = str_replace('m','𝚖',$marcus); 
$marcus = str_replace('n','𝚗',$marcus); 
$marcus = str_replace('o','𝚘',$marcus); 
$marcus = str_replace('p','𝚙',$marcus); 
$marcus = str_replace('q','𝚚',$marcus); 
$marcus = str_replace('r','𝚛',$marcus); 
$marcus = str_replace('s','𝚜',$marcus); 
$marcus = str_replace('t','𝚝',$marcus); 
$marcus = str_replace('u','𝚞',$marcus); 
$marcus = str_replace('v','𝚟',$marcus); 
$marcus = str_replace('w','𝚠',$marcus); 
$marcus = str_replace('x','𝚡',$marcus); 
$marcus = str_replace('y','𝚢',$marcus); 
$marcus = str_replace('z','𝚣',$marcus);
$marcus = str_replace('A','𝙰',$marcus); 
$marcus = str_replace('B','𝙱',$marcus); 
$marcus = str_replace('C','𝙲',$marcus); 
$marcus = str_replace('D','𝙳',$marcus); 
$marcus = str_replace('E','𝙴',$marcus); 
$marcus = str_replace('F','𝙵',$marcus); 
$marcus = str_replace('G','𝙶',$marcus); 
$marcus = str_replace('H','𝙷',$marcus); 
$marcus = str_replace('I','𝙸',$marcus); 
$marcus = str_replace('J','𝙹',$marcus); 
$marcus = str_replace('K','𝙺',$marcus); 
$marcus = str_replace('L','𝙻',$marcus); 
$marcus = str_replace('M','𝙼',$marcus); 
$marcus = str_replace('N','𝙽',$marcus); 
$marcus = str_replace('O','𝙾',$marcus); 
$marcus = str_replace('P','𝙿',$marcus); 
$marcus = str_replace('Q','𝚀',$marcus); 
$marcus = str_replace('R','𝚁',$marcus); 
$marcus = str_replace('S','𝚂',$marcus); 
$marcus = str_replace('T','𝚃',$marcus); 
$marcus = str_replace('U','𝚄',$marcus); 
$marcus = str_replace('V','𝚅',$marcus); 
$marcus = str_replace('W','𝚆',$marcus); 
$marcus = str_replace('X','𝚇',$marcus); 
$marcus = str_replace('Y','𝚈',$marcus); 
$marcus = str_replace('Z','𝚉',$marcus);
bot('sendMessage',[ 
'chat_id'=>$chatId,
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$messageId,
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('a', '𝙖', $messageText);
$marcus = str_replace('b', '𝙗', $marcus);
$marcus = str_replace('c', '𝙘', $marcus);
$marcus = str_replace('d', '𝙙', $marcus);
$marcus = str_replace('e', '𝙚', $marcus);
$marcus = str_replace('f', '𝙛', $marcus);
$marcus = str_replace('g', '𝙜', $marcus);
$marcus = str_replace('h', '𝙝', $marcus);
$marcus = str_replace('i', '𝙞', $marcus);
$marcus = str_replace('j', '𝒋', $marcus);
$marcus = str_replace('k', '𝙠', $marcus);
$marcus = str_replace('l', '𝙡', $marcus);
$marcus = str_replace('m', '𝙢', $marcus);
$marcus = str_replace('n', '𝙣', $marcus);
$marcus = str_replace('o', '𝙤', $marcus);
$marcus = str_replace('p', '𝙥', $marcus);
$marcus = str_replace('q', '𝙦', $marcus);
$marcus = str_replace('r', '𝙧', $marcus);
$marcus = str_replace('s', '𝙨', $marcus);
$marcus = str_replace('t', '𝙩', $marcus);
$marcus = str_replace('u', '𝙪', $marcus);
$marcus = str_replace('v', '𝙫', $marcus);
$marcus = str_replace('w', '𝙬', $marcus);
$marcus = str_replace('x', '𝙭', $marcus);
$marcus = str_replace('y', '𝙮', $marcus);
$marcus = str_replace('z', '𝙯', $marcus);
$marcus = str_replace('A', '𝘼', $marcus);
$marcus = str_replace('B', '𝘽', $marcus);
$marcus = str_replace('C', '𝘾', $marcus);
$marcus = str_replace('D', '𝘿', $marcus);
$marcus = str_replace('E', '𝙀', $marcus);
$marcus = str_replace('F', '𝙁', $marcus);
$marcus = str_replace('G', '𝙂', $marcus);
$marcus = str_replace('H', '𝙃', $marcus);
$marcus = str_replace('I', '𝙄', $marcus);
$marcus = str_replace('J', '𝙅', $marcus);
$marcus = str_replace('K', '𝙆', $marcus);
$marcus = str_replace('L', '𝙇', $marcus);
$marcus = str_replace('M', '𝙈', $marcus);
$marcus = str_replace('N', '𝙉', $marcus);
$marcus = str_replace('O', '𝙊', $marcus);
$marcus = str_replace('P', '𝙋', $marcus);
$marcus = str_replace('Q', '𝙌', $marcus);
$marcus = str_replace('R', '𝙍', $marcus);
$marcus = str_replace('S', '𝙎', $marcus);
$marcus = str_replace('T', '𝙏', $marcus);
$marcus = str_replace('U', '𝙐', $marcus);
$marcus = str_replace('V', '𝙑', $marcus);
$marcus = str_replace('W', '𝙒', $marcus);
$marcus = str_replace('X', '𝙓', $marcus);
$marcus = str_replace('Y', '𝙔', $marcus);
$marcus = str_replace('Z', '𝙕', $marcus);
bot('sendMessage',[
'chat_id'=>$chatId, 
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$messageId,
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('a', '𝗮', $messageText);
$marcus = str_replace('b', '𝗯', $marcus);
$marcus = str_replace('c', '𝗰', $marcus);
$marcus = str_replace('d', '𝗱', $marcus);
$marcus = str_replace('e', '𝗲', $marcus);
$marcus = str_replace('f', '𝗳', $marcus);
$marcus = str_replace('g', '𝗴', $marcus);
$marcus = str_replace('h', '𝗵', $marcus);
$marcus = str_replace('i', '𝗶', $marcus);
$marcus = str_replace('j', '𝗷', $marcus);
$marcus = str_replace('k', '𝗸', $marcus);
$marcus = str_replace('l', '𝗹', $marcus);
$marcus = str_replace('m', '𝗺', $marcus);
$marcus = str_replace('n', '𝗻', $marcus);
$marcus = str_replace('o', '𝗼', $marcus);
$marcus = str_replace('p', '𝗽', $marcus);
$marcus = str_replace('q', '𝗾', $marcus);
$marcus = str_replace('r', '𝗿', $marcus);
$marcus = str_replace('s', '𝘀', $marcus);
$marcus = str_replace('t', '𝘁', $marcus);
$marcus = str_replace('u', '𝘂', $marcus);
$marcus = str_replace('v', '𝘃', $marcus);
$marcus = str_replace('w', '𝘄', $marcus);
$marcus = str_replace('x', '𝘅', $marcus);
$marcus = str_replace('y', '𝘆', $marcus);
$marcus = str_replace('z', '𝘇', $marcus);
$marcus = str_replace('A', '𝗔', $marcus);
$marcus = str_replace('B', '𝗕', $marcus);
$marcus = str_replace('C', '𝗖', $marcus);
$marcus = str_replace('D', '𝗗', $marcus);
$marcus = str_replace('E', '𝗘', $marcus);
$marcus = str_replace('F', '𝗙', $marcus);
$marcus = str_replace('G', '𝗚', $marcus);
$marcus = str_replace('H', '𝗛', $marcus);
$marcus = str_replace('I', '𝗜', $marcus);
$marcus = str_replace('J', '𝗝', $marcus);
$marcus = str_replace('K', '𝗞', $marcus);
$marcus = str_replace('L', '𝗟', $marcus);
$marcus = str_replace('M', '𝗠', $marcus);
$marcus = str_replace('N', '𝗡', $marcus);
$marcus = str_replace('O', '𝗢', $marcus);
$marcus = str_replace('P', '𝗣', $marcus);
$marcus = str_replace('Q', '𝗤', $marcus);
$marcus = str_replace('R', '𝗥', $marcus);
$marcus = str_replace('S', '𝗦', $marcus);
$marcus = str_replace('T', '𝗧', $marcus);
$marcus = str_replace('U', '𝗨', $marcus);
$marcus = str_replace('V', '𝗩', $marcus);
$marcus = str_replace('W', '𝗪', $marcus);
$marcus = str_replace('X', '𝗫', $marcus);
$marcus = str_replace('Y', '𝗬', $marcus);
$marcus = str_replace('Z', '𝗭', $marcus);
bot('sendMessage',[
'chat_id'=>$chatId, 
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$messageId,
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('a', '𝔞', $messageText);
$marcus = str_replace('b', '𝔟', $marcus);
$marcus = str_replace('c', '𝔠', $marcus);
$marcus = str_replace('d', '𝔡', $marcus);
$marcus = str_replace('e', '𝔢', $marcus);
$marcus = str_replace('f', '𝔣', $marcus);
$marcus = str_replace('g', '𝔤', $marcus);
$marcus = str_replace('h', '𝔥', $marcus);
$marcus = str_replace('i', '𝔦', $marcus);
$marcus = str_replace('j', '𝔧', $marcus);
$marcus = str_replace('k', '𝔨', $marcus);
$marcus = str_replace('l', '𝔦', $marcus);
$marcus = str_replace('m', '𝔪', $marcus);
$marcus = str_replace('n', '𝔫', $marcus);
$marcus = str_replace('o', '𝔬', $marcus);
$marcus = str_replace('p', '𝔭', $marcus);
$marcus = str_replace('q', '𝔮', $marcus);
$marcus = str_replace('r', '𝔯', $marcus);
$marcus = str_replace('s', '𝔰', $marcus);
$marcus = str_replace('t', '𝔱', $marcus);
$marcus = str_replace('u', '𝔲', $marcus);
$marcus = str_replace('v', '𝔳', $marcus);
$marcus = str_replace('w', '𝔴', $marcus);
$marcus = str_replace('x', '𝔵', $marcus);
$marcus = str_replace('y', '𝔶', $marcus);
$marcus = str_replace('z', '𝔷', $marcus);
$marcus = str_replace('A', '𝔞', $marcus);
$marcus = str_replace('B', '𝔟', $marcus);
$marcus = str_replace('C', '𝔠', $marcus);
$marcus = str_replace('D', '𝔡', $marcus);
$marcus = str_replace('E', '𝔢', $marcus);
$marcus = str_replace('F', '𝔣', $marcus);
$marcus = str_replace('G', '𝔤', $marcus);
$marcus = str_replace('H', '𝔥', $marcus);
$marcus = str_replace('I', '𝔦', $marcus);
$marcus = str_replace('J', '𝔧', $marcus);
$marcus = str_replace('K', '𝔨', $marcus);
$marcus = str_replace('L', '𝔩', $marcus);
$marcus = str_replace('M', '𝔪', $marcus);
$marcus = str_replace('N', '𝔫', $marcus);
$marcus = str_replace('O', '𝔬', $marcus);
$marcus = str_replace('P', '𝔭', $marcus);
$marcus = str_replace('Q', '𝔮', $marcus);
$marcus = str_replace('R', '𝔯', $marcus);
$marcus = str_replace('S', '𝔰', $marcus);
$marcus = str_replace('T', '𝔱', $marcus);
$marcus = str_replace('U', '𝔲', $marcus);
$marcus = str_replace('V', '𝔳', $marcus);
$marcus = str_replace('W', '𝔴', $marcus);
$marcus = str_replace('X', '𝔵', $marcus);
$marcus = str_replace('Y', '𝔶', $marcus);
$marcus = str_replace('Z', '𝔷', $marcus);
bot('sendMessage',[
'chat_id'=>$chatId, 
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$messageId,
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('a', '𝒂', $messageText);
$marcus = str_replace('b', '𝒃', $marcus);
$marcus = str_replace('c', '𝒄', $marcus);
$marcus = str_replace('d', '𝒅', $marcus);
$marcus = str_replace('e', '𝒆', $marcus);
$marcus = str_replace('f', '𝒇', $marcus);
$marcus = str_replace('g', '𝒈', $marcus);
$marcus = str_replace('h', '𝒉', $marcus);
$marcus = str_replace('i', '𝒊', $marcus);
$marcus = str_replace('j', '𝒋', $marcus);
$marcus = str_replace('k', '𝒌', $marcus);
$marcus = str_replace('l', '𝒍', $marcus);
$marcus = str_replace('m', '𝒎', $marcus);
$marcus = str_replace('n', '𝒏', $marcus);
$marcus = str_replace('o', '𝒐', $marcus);
$marcus = str_replace('p', '𝒑', $marcus);
$marcus = str_replace('q', '𝒒', $marcus);
$marcus = str_replace('r', '𝒓', $marcus);
$marcus = str_replace('s', '𝒔', $marcus);
$marcus = str_replace('t', '𝒕', $marcus);
$marcus = str_replace('u', '𝒖', $marcus);
$marcus = str_replace('v', '𝒗', $marcus);
$marcus = str_replace('w', '𝒘', $marcus);
$marcus = str_replace('x', '𝒙', $marcus);
$marcus = str_replace('y', '𝒚', $marcus);
$marcus = str_replace('z', '𝒛', $marcus);
$marcus = str_replace('A', '𝑨', $marcus);
$marcus = str_replace('B', '𝑩', $marcus);
$marcus = str_replace('C', '𝑪', $marcus);
$marcus = str_replace('D', '𝑫', $marcus);
$marcus = str_replace('E', '𝑬', $marcus);
$marcus = str_replace('F', '𝑭', $marcus);
$marcus = str_replace('G', '𝑮', $marcus);
$marcus = str_replace('H', '𝑯', $marcus);
$marcus = str_replace('I', '𝑰', $marcus);
$marcus = str_replace('J', '𝑱', $marcus);
$marcus = str_replace('K', '𝑲', $marcus);
$marcus = str_replace('L', '𝑳', $marcus);
$marcus = str_replace('M', '𝑴', $marcus);
$marcus = str_replace('N', '𝑵', $marcus);
$marcus = str_replace('O', '𝑶', $marcus);
$marcus = str_replace('P', '𝑷', $marcus);
$marcus = str_replace('Q', '𝑸', $marcus);
$marcus = str_replace('R', '𝑹', $marcus);
$marcus = str_replace('S', '𝑺', $marcus);
$marcus = str_replace('T', '𝑻', $marcus);
$marcus = str_replace('U', '𝑼', $marcus);
$marcus = str_replace('V', '𝑽', $marcus);
$marcus = str_replace('W', '𝑾', $marcus);
$marcus = str_replace('X', '𝑿', $marcus);
$marcus = str_replace('Y', '𝒀', $marcus);
$marcus = str_replace('Z', '𝒁', $marcus);
bot('sendMessage',[
'chat_id'=>$chatId, 
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$messageId,
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText); 
$marcus = str_replace('a','𝘢',$messageText);
$marcus = str_replace("b","𝘣",$marcus);
$marcus = str_replace("c","𝘤",$marcus);
$marcus = str_replace("d","𝘥",$marcus);
$marcus = str_replace("e","𝘦",$marcus);
$marcus = str_replace("f","𝘧",$marcus);
$marcus = str_replace("g","𝘨",$marcus);
$marcus = str_replace("h","𝘩",$marcus);
$marcus = str_replace("i","𝘪",$marcus);
$marcus = str_replace("j","𝘫",$marcus);
$marcus = str_replace("k","𝘬",$marcus);
$marcus = str_replace("l","𝘭",$marcus);
$marcus = str_replace("m","𝘮",$marcus);
$marcus = str_replace("n","𝘯",$marcus);
$marcus = str_replace("o","𝘰",$marcus);
$marcus = str_replace("p","𝘱",$marcus);
$marcus = str_replace("q","𝘲",$marcus);
$marcus = str_replace("r","𝘳",$marcus);
$marcus = str_replace("s","𝘴",$marcus);
$marcus = str_replace("t","𝘵",$marcus);
$marcus = str_replace("u","𝘶",$marcus);
$marcus = str_replace("v","𝘷",$marcus);
$marcus = str_replace("w","𝘸",$marcus);
$marcus = str_replace("x","𝘹",$marcus);
$marcus = str_replace("y","𝘺",$marcus);
$marcus = str_replace("z","𝘻",$marcus);
$marcus = str_replace('A','𝘈',$marcus);
$marcus = str_replace("B","𝘉",$marcus);
$marcus = str_replace("C","𝘊",$marcus);
$marcus = str_replace("D","𝘋",$marcus);
$marcus = str_replace("E","𝘌",$marcus);
$marcus = str_replace("F","𝘍",$marcus);
$marcus = str_replace("G","𝘎",$marcus);
$marcus = str_replace("H","𝘏",$marcus);
$marcus = str_replace("I","𝘐",$marcus);
$marcus = str_replace("J","𝘑",$marcus);
$marcus = str_replace("K","𝘒",$marcus);
$marcus = str_replace("L","𝘓",$marcus);
$marcus = str_replace("M","𝘔",$marcus);
$marcus = str_replace("N","𝘕",$marcus);
$marcus = str_replace("O","𝘖",$marcus);
$marcus = str_replace("P","𝘗",$marcus);
$marcus = str_replace("Q","𝘘",$marcus);
$marcus = str_replace("R","𝘙",$marcus);
$marcus = str_replace("S","𝘚",$marcus);
$marcus = str_replace("T","𝘛",$marcus);
$marcus = str_replace("U","𝘜",$marcus);
$marcus = str_replace("V","𝘝",$marcus);
$marcus = str_replace("W","𝘞",$marcus);
$marcus = str_replace("X","𝘟",$marcus);
$marcus = str_replace("Y","𝘠",$marcus);
$marcus = str_replace("Z","𝘡",$marcus);
bot('sendMessage',[
'chat_id'=>$chatId,
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$messageId,
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('a','𝖺',$messageText);
$marcus = str_replace("b","𝖻",$marcus);
$marcus = str_replace("c","𝖼",$marcus);
$marcus = str_replace("d","𝖽",$marcus);
$marcus = str_replace("e","𝖾",$marcus);
$marcus = str_replace("f","𝖿",$marcus);
$marcus = str_replace("g","𝗀",$marcus);
$marcus = str_replace("h","𝗁",$marcus);
$marcus = str_replace("i","𝗂",$marcus);
$marcus = str_replace("j","𝗃",$marcus);
$marcus = str_replace("k","𝗄",$marcus);
$marcus = str_replace("l","𝗅",$marcus);
$marcus = str_replace("m","𝗆",$marcus);
$marcus = str_replace("n","𝗇",$marcus);
$marcus = str_replace("o","𝗈",$marcus);
$marcus = str_replace("p","𝗉",$marcus);
$marcus = str_replace("q","𝗊",$marcus);
$marcus = str_replace("r","𝗋",$marcus);
$marcus = str_replace("s","𝗌",$marcus);
$marcus = str_replace("t","𝗍",$marcus);
$marcus = str_replace("u","𝗎",$marcus);
$marcus = str_replace("v","𝗏",$marcus);
$marcus = str_replace("w","𝗐",$marcus);
$marcus = str_replace("x","𝗑",$marcus);
$marcus = str_replace("y","𝗒",$marcus);
$marcus = str_replace("z","𝗓",$marcus);
$marcus = str_replace('A','𝖠',$marcus);
$marcus = str_replace("B","𝖡",$marcus);
$marcus = str_replace("C","𝖢",$marcus);
$marcus = str_replace("D","𝖣",$marcus);
$marcus = str_replace("E","𝖤",$marcus);
$marcus = str_replace("F","𝖥",$marcus);
$marcus = str_replace("G","𝖦",$marcus);
$marcus = str_replace("H","𝖧",$marcus);
$marcus = str_replace("I","𝖨",$marcus);
$marcus = str_replace("J","𝖩",$marcus);
$marcus = str_replace("K","𝖪",$marcus);
$marcus = str_replace("L","𝖫",$marcus);
$marcus = str_replace("M","𝖬",$marcus);
$marcus = str_replace("N","𝖭",$marcus);
$marcus = str_replace("O","𝖮",$marcus);
$marcus = str_replace("P","𝖯",$marcus);
$marcus = str_replace("Q","𝖰",$marcus);
$marcus = str_replace("R","𝖱",$marcus);
$marcus = str_replace("S","𝖲",$marcus);
$marcus = str_replace("T","𝖳",$marcus);
$marcus = str_replace("U","𝖴",$marcus);
$marcus = str_replace("V","𝖵",$marcus);
$marcus = str_replace("W","𝖶",$marcus);
$marcus = str_replace("X","𝖷",$marcus);
$marcus = str_replace("Y","𝖸",$marcus);
$marcus = str_replace("Z","𝖹",$marcus);
 bot('sendMessage',[ 
'chat_id'=>$chatId, 
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$messageId,
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('a','𝖆',$messageText); 
$marcus = str_replace('b','𝖇',$marcus); 
$marcus = str_replace('c','𝖈',$marcus); 
$marcus = str_replace('d','𝖉',$marcus); 
$marcus = str_replace('e','𝖊',$marcus); 
$marcus = str_replace('f','𝖋',$marcus); 
$marcus = str_replace('g','𝖌',$marcus); 
$marcus = str_replace('h','𝖍',$marcus); 
$marcus = str_replace('i','𝖎',$marcus); 
$marcus = str_replace('j','𝖏',$marcus); 
$marcus = str_replace('k','𝖐',$marcus); 
$marcus = str_replace('l','𝖑',$marcus); 
$marcus = str_replace('m','𝖒',$marcus); 
$marcus = str_replace('n','𝖓',$marcus); 
$marcus = str_replace('o','𝖔',$marcus); 
$marcus = str_replace('p','𝖕',$marcus); 
$marcus = str_replace('q','𝖖',$marcus); 
$marcus = str_replace('r','𝖗',$marcus); 
$marcus = str_replace('s','𝖘',$marcus); 
$marcus = str_replace('t','𝖙',$marcus); 
$marcus = str_replace('u','𝖚',$marcus); 
$marcus = str_replace('v','𝖛',$marcus); 
$marcus = str_replace('w','𝖜',$marcus); 
$marcus = str_replace('x','𝖝',$marcus); 
$marcus = str_replace('y','𝖞',$marcus); 
$marcus = str_replace('z','𝖟',$marcus);
$marcus = str_replace('A','𝖆',$marcus); 
$marcus = str_replace('B','𝖇',$marcus); 
$marcus = str_replace('C','𝖈',$marcus); 
$marcus = str_replace('D','𝖉',$marcus); 
$marcus = str_replace('E','𝖊',$marcus); 
$marcus = str_replace('F','𝖋',$marcus); 
$marcus = str_replace('G','𝖌',$marcus); 
$marcus = str_replace('H','𝖍',$marcus); 
$marcus = str_replace('I','𝖎',$marcus); 
$marcus = str_replace('J','𝖏',$marcus); 
$marcus = str_replace('K','𝖐',$marcus); 
$marcus = str_replace('L','𝖑',$marcus); 
$marcus = str_replace('M','𝖒',$marcus); 
$marcus = str_replace('N','𝖓',$marcus); 
$marcus = str_replace('O','𝖔',$marcus); 
$marcus = str_replace('P','𝖕',$marcus); 
$marcus = str_replace('Q','𝖖',$marcus); 
$marcus = str_replace('R','𝖗',$marcus); 
$marcus = str_replace('S','𝖘',$marcus); 
$marcus = str_replace('T','𝖙',$marcus); 
$marcus = str_replace('U','𝖚',$marcus); 
$marcus = str_replace('V','𝖛',$marcus); 
$marcus = str_replace('W','𝖜',$marcus); 
$marcus = str_replace('X','𝖝',$marcus); 
$marcus = str_replace('Y','𝖞',$marcus); 
$marcus = str_replace('Z','𝖟',$marcus);
bot('sendMessage',[ 
'chat_id'=>$chatId,
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$messageId,
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('a','𝐚',$messageText);
$marcus = str_replace("b","𝐛",$marcus);
$marcus = str_replace("c","𝐜",$marcus);
$marcus = str_replace("d","𝐝",$marcus);
$marcus = str_replace("e","𝐞",$marcus);
$marcus = str_replace("f","𝐟",$marcus);
$marcus = str_replace("g","𝐠",$marcus);
$marcus = str_replace("h","𝐡",$marcus);
$marcus = str_replace("i","𝐢",$marcus);
$marcus = str_replace("j","𝐣",$marcus);
$marcus = str_replace("k","𝐤",$marcus);
$marcus = str_replace("l","𝐥",$marcus);
$marcus = str_replace("m","𝐦",$marcus);
$marcus = str_replace("n","𝐧",$marcus);
$marcus = str_replace("o","𝐨",$marcus);
$marcus = str_replace("p","𝐩",$marcus);
$marcus = str_replace("q","𝐪",$marcus);
$marcus = str_replace("r","𝐫",$marcus);
$marcus = str_replace("s","𝐬",$marcus);
$marcus = str_replace("t","𝐭",$marcus);
$marcus = str_replace("u","𝐮",$marcus);
$marcus = str_replace("v","𝐯",$marcus);
$marcus = str_replace("w","𝐰",$marcus);
$marcus = str_replace("x","𝐱",$marcus);
$marcus = str_replace("y","𝐲",$marcus);
$marcus = str_replace("z","𝐳",$marcus);
$marcus = str_replace('A','𝐀',$marcus);
$marcus = str_replace("B","𝐁",$marcus);
$marcus = str_replace("C","𝐂",$marcus);
$marcus = str_replace("D","𝐃",$marcus);
$marcus = str_replace("E","𝐄",$marcus);
$marcus = str_replace("F","𝐅",$marcus);
$marcus = str_replace("G","𝐆",$marcus);
$marcus = str_replace("H","𝐇",$marcus);
$marcus = str_replace("I","𝐈",$marcus);
$marcus = str_replace("J","𝐉",$marcus);
$marcus = str_replace("K","𝐊",$marcus);
$marcus = str_replace("L","𝑳",$marcus);
$marcus = str_replace("M","𝐌",$marcus);
$marcus = str_replace("N","𝐍",$marcus);
$marcus = str_replace("O","𝐎",$marcus);
$marcus = str_replace("P","𝐏",$marcus);
$marcus = str_replace("Q","𝐐",$marcus);
$marcus = str_replace("R","𝐑",$marcus);
$marcus = str_replace("S","𝐒",$marcus);
$marcus = str_replace("T","𝐓",$marcus);
$marcus = str_replace("U","𝐔",$marcus);
$marcus = str_replace("V","𝐕",$marcus);
$marcus = str_replace("W","𝐖",$marcus);
$marcus = str_replace("X","𝐗",$marcus);
$marcus = str_replace("Y","𝐘",$marcus);
$marcus = str_replace("Z","𝐙",$marcus);
 bot('sendMessage',[ 
'chat_id'=>$chatId, 
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$messageId,
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('a','𝕒',$messageText);
$marcus = str_replace("b","𝕓",$marcus);
$marcus = str_replace("c","𝕔",$marcus);
$marcus = str_replace("d","𝕕",$marcus);
$marcus = str_replace("e","𝕖",$marcus);
$marcus = str_replace("f","𝕗",$marcus);
$marcus = str_replace("g","𝕘",$marcus);
$marcus = str_replace("h","𝕙",$marcus);
$marcus = str_replace("i","𝕚",$marcus);
$marcus = str_replace("j","𝕛",$marcus);
$marcus = str_replace("k","𝕜",$marcus);
$marcus = str_replace("l","𝕝",$marcus);
$marcus = str_replace("m","𝕞",$marcus);
$marcus = str_replace("n","𝕟",$marcus);
$marcus = str_replace("o","𝕠",$marcus);
$marcus = str_replace("p","𝕡",$marcus);
$marcus = str_replace("q","𝕢",$marcus);
$marcus = str_replace("r","𝕣",$marcus);
$marcus = str_replace("s","𝕤",$marcus);
$marcus = str_replace("t","𝕥",$marcus);
$marcus = str_replace("u","𝕦",$marcus);
$marcus = str_replace("v","𝕧",$marcus);
$marcus = str_replace("w","𝕨",$marcus);
$marcus = str_replace("x","𝕩",$marcus);
$marcus = str_replace("y","𝕪",$marcus);
$marcus = str_replace("z","𝕫",$marcus);
$marcus = str_replace('A','𝔸',$marcus);
$marcus = str_replace("B","𝔹",$marcus);
$marcus = str_replace("C","ℂ",$marcus);
$marcus = str_replace("D","𝔻",$marcus);
$marcus = str_replace("E","𝔼",$marcus);
$marcus = str_replace("F","𝔽",$marcus);
$marcus = str_replace("G","𝔾",$marcus);
$marcus = str_replace("H","ℍ",$marcus);
$marcus = str_replace("I","𝕀",$marcus);
$marcus = str_replace("J","𝕁",$marcus);
$marcus = str_replace("K","𝕂",$marcus);
$marcus = str_replace("L","𝕃",$marcus);
$marcus = str_replace("M","𝕄",$marcus);
$marcus = str_replace("N","ℕ",$marcus);
$marcus = str_replace("O","𝕆",$marcus);
$marcus = str_replace("P","ℙ",$marcus);
$marcus = str_replace("Q","ℚ",$marcus);
$marcus = str_replace("R","ℝ",$marcus);
$marcus = str_replace("S","𝕊",$marcus);
$marcus = str_replace("T","𝕋",$marcus);
$marcus = str_replace("U","𝕌",$marcus);
$marcus = str_replace("V","𝕍",$marcus);
$marcus = str_replace("W","𝕎",$marcus);
$marcus = str_replace("X","𝕏",$marcus);
$marcus = str_replace("Y","𝕐",$marcus);
$marcus = str_replace("Z","ℤ",$marcus);
 bot('sendMessage',[ 
'chat_id'=>$chatId, 
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
'reply_to_message_id'=>$messageId,
]);
 bot('sendMessage',[
'chat_id'=>$chatId,
'text'=>"*
• تم زخرفة الاسم $messageText
• ويمكنك رؤية الرموز ايضاً بالضغط على رموز 
• يرجى الضغط على زر عوده في الاسفل
*"
,'parse_mode'=>"markdown",'disable_web_page_preview'=>true,'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'الرموز' ,'callback_data'=>"at"],['text'=>'رجوع' ,'callback_data'=>"home"]],
[['text'=>'زخرفة مرة اخرى ✹' ,'callback_data'=>"ww"]],
]])
]);
unset($using[$chatId]);
file_put_contents("using.json",json_encode($using,128|32|256));
}
} 
if($data == "at"){
bot('editMessageText',[
 'chat_id'=>$chatId,
 'message_id'=>$message_id,
'text'=>"𝟏 𝟐 𝟑 𝟒 𝟓 𝟔 𝟕 𝟖 𝟗 𝟎 ﷼ ﷻ ﷽ ✞ ッ ッ 彡 Ω ۞ ۩ ✟ 『』۝ Ξ 道 凸 父 个 ¤ 品 〠 ๛ 𖤍 ᶠᶸᶜᵏᵧₒᵤ ࿐ ⍆ ⍅ ⇭ ༒   𖠃 𖠅 𖠆 𖠊 𖡒 𖡗 𖣩 ꧁ ꧂〰 𖥓 𖥏 𖥎 𖥌 𖥋 𖥊 𖥈 𖥅 𖥃 𖥂 𖥀 𖤼 𖤹 𖤸 𖤷 𖤶 𖤭 𖤫 𖤪 𖤨 𖤧 𖤥 𖤤 𖤣 𖤢 𖤡 𖤟 𖤞 𖤝 𖤜 𖤛 𖤚 𖤘 𖤙 𖤗 𖤕 𖤓 𖤒 𖤐 ဏ ࿘ ࿗ ࿖ ࿕ ࿑ ࿌ ࿋ ࿊ ࿉ ࿈ ࿇ ࿅ ࿄ ࿃ ࿂ ༼ ༽ ༺ ༻ ༗ ༖ ༕ ⏝ ⏜ ⏎ ၄ ߷ ܛ ׀
𖠀 𖠁 𖠂 𖠅 𖠆 𖠇 𖠈 𖠉 𖠍 𖠎 𖠏 𖠐 𖠑 𖠒 𖠓 𖠔 𖠕 𖠖 𖠗 𖠘 𖠙 𖠚 𖠛 𖠜 𖠝 𖠞 𖠟 𖠠 𖠡 ?? 𖠣 𖠤 𖠥 𖠦 𖠧 𖠨 𖠩 𖠪 𖠫 𖠬 𖠭 𖠮 𖠯 𖠰 𖠱 𖠲 𖠳 𖠴 𖠵 𖠶 𖠷 𖠸 𖠹 𖠺 𖠻 𖠼 𖠽 𖠾 𖠿 𖡀 𖡁 𖡂 𖡃 𖡄 𖡅 𖡆 𖡇 𖡈 𖡉 𖡊 𖡋 𖡌 𖡍 𖡎 𖡏 𖡐 𖡑 𖡒 𖡓 𖡔 𖡕 𖡖 𖡗 𖡘 𖡙 𖡚 𖡛 𖡜 𖡝 𖡞 𖡟 𖡠 𖡡 𖡢 𖡣 𖡤 𖡥 𖡦 𖡧 𖡨 𖡩 𖡪 𖡫 𖡬 𖡭 𖡮 𖡯 𖡰 𖡱 𖡲 𖡳 𖡴 𖡵 𖡶 𖡷 𖡸 𖡹 𖡺 𖡻 𖡼 𖡽 𖡾 𖡿 𖢀 𖢁 𖢂 𖢃 𖢄 𖢅 𖢆 𖢇 𖢈 𖢉 𖢊 𖢋 𖢌 𖢍 𖢎 𖢏 𖢐 𖢑 𖢒 𖢓 𖢔 𖢕 𖢖 𖢗 𖢘 𖢙 𖢚 𖢛 𖢜 𖢝 𖢞 𖢟 𖢠 𖢡 𖢢 𖢣 𖢤 𖢥 𖢦 𖢧 𖢨 𖢩 𖢪 𖢫 𖢬 𖢭 𖢮 𖢯 𖢰 𖢱 𖢲 𖢳 𖢴 𖢵 𖢶 𖢷 𖢸 𖢹 𖢺 𖢻 𖢼 𖢽 𖢾 𖢿 𖣀 𖣁 𖣂 𖣃 𖣄 𖣅 𖣆 𖣇 𖣈 𖣉 𖣊 𖣋 𖣌 𖣍 𖣎 𖣏 𖣐 𖣑 𖣒 𖣓 𖣔 𖣕 𖣖 𖣗 𖣘 𖣙 𖣚 𖣛 𖣜 𖣝 𖣞 𖣟 𖣠 𖣡 𖣢 𖣣 𖣤 𖣥 𖣦 𖣧 𖣨 𖣩 𖣪 𖣫 𖣬 𖣭 𖣮 𖣯 𖣰 𖣱 𖣲 𖣳 𖣴 𖣵 𖣶 𖣷 𖣸 𖣹 𖣺 𖣻 𖣼 𖣽 𖣾 𖣿
 ",'reply_markup'=>json_encode([ 
'inline_keyboard'=>[
[['text'=>'🔙' ,'callback_data'=>"home"]],
]])
]);
}
if(!preg_match('/([a-z])|([A-Z])/i',$messageText)){
	if($hmd == 'Arabic'){
            bot('sendmessage',[
  'chat_id'=>$chatId,
  'text'=>"",
  ]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText); 
$marcus = str_replace('ض','ض֮',$messageText);
$marcus = str_replace('ص','ص֓',$marcus); 
$marcus = str_replace('ث','ثֻ',$marcus); 
$marcus = str_replace('ق','ق֯',$marcus); 
$marcus = str_replace('ف','ف֛',$marcus); 
$marcus = str_replace('غ','غؒ',$marcus); 
$marcus = str_replace('ع','عٌ',$marcus); 
$marcus = str_replace('ه','هٞ',$marcus); 
$marcus = str_replace('خ','خ٘٘٘٘٘٘٘٘٘٘',$marcus); 
$marcus = str_replace('ح','حٟ',$marcus); 
$marcus = str_replace('ج','جۤ',$marcus); 
$marcus = str_replace('ش','شۨ',$marcus);
$marcus = str_replace('س','سܱܰ',$marcus); 
$marcus = str_replace('ي','يަ',$marcus); 
$marcus = str_replace('ب','ب߬',$marcus); 
$marcus = str_replace('ل','ل',$marcus); 
$marcus = str_replace('ا','اࠗ',$marcus); 
$marcus = str_replace('ت','ت',$marcus); 
$marcus = str_replace('ن','نۨۨۨۨۨۨۨۨ',$marcus); 
$marcus = str_replace('م','مࣩ',$marcus); 
$marcus = str_replace('ك','ك๊',$marcus); 
$marcus = str_replace('ظ','ظ້',$marcus); 
$marcus = str_replace('ط','ط็',$marcus); 
$marcus = str_replace('ذ','ذྃ',$marcus); 
$marcus = str_replace('ء','ء',$marcus); 
$marcus = str_replace('ؤ','ؤ',$marcus); 
$marcus = str_replace('ر','ر',$marcus); 
$marcus = str_replace('ى','ى',$marcus); 
$marcus = str_replace('ة','ة',$marcus); 
$marcus = str_replace('و','୨و',$marcus); 
$marcus = str_replace('ز','ز',$marcus); 
$marcus = str_replace('ظ',' ظ',$marcus); 
$marcus = str_replace('د','د',$marcus); 
 bot('sendMessage',[ 
'chat_id'=>$chatId, 
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('ض','ضّ',$messageText);
$marcus = str_replace('ص','صٌ',$marcus); 
$marcus = str_replace('ث','ثّ',$marcus); 
$marcus = str_replace('ق','قِ',$marcus); 
$marcus = str_replace('ف','فِّ',$marcus); 
$marcus = str_replace('غ','غٌ',$marcus); 
$marcus = str_replace('ع','عٌ',$marcus); 
$marcus = str_replace('ه','هِ',$marcus); 
$marcus = str_replace('خ','خَ',$marcus); 
$marcus = str_replace('ح','حٌ',$marcus); 
$marcus = str_replace('ج','جِ',$marcus); 
$marcus = str_replace('ش','شٍ',$marcus); 
$marcus = str_replace('س',' س',$marcus); 
$marcus = str_replace('ي','يِّ',$marcus); 
$marcus = str_replace('ب','بِ',$marcus);
$marcus = str_replace('ل','لَ',$marcus); 
$marcus = str_replace('ا','أّ',$marcus); 
$marcus = str_replace('ت','تّ',$marcus); 
$marcus = str_replace('ن','نِ',$marcus); 
$marcus = str_replace('ك','ګ',$marcus); 
$marcus = str_replace('م','مَ',$marcus); 
$marcus = str_replace('ة',' ةّ',$marcus); 
$marcus = str_replace('ء','ء',$marcus); 
$marcus = str_replace('ظ','ظّ',$marcus); 
$marcus = str_replace('ط','طّ',$marcus); 
$marcus = str_replace('ذ','ذّ',$marcus); 
$marcus = str_replace('د','دِ',$marcus); 
$marcus = str_replace('ز','زِّ',$marcus); 
$marcus = str_replace('ر','ڒٍ',$marcus); 
$marcus = str_replace('و','وِ',$marcus); 
$marcus = str_replace('ى','ىّ',$marcus);
bot('sendMessage',[ 
'chat_id'=>$chatId,
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('ض','ضّ',$messageText);
$marcus = str_replace('ص','صٌ',$marcus); 
$marcus = str_replace('ث','ثّ',$marcus); 
$marcus = str_replace('ق','قᮭ',$marcus); 
$marcus = str_replace('ف','ف᭫ᮥ',$marcus); 
$marcus = str_replace('غ','غٌ',$marcus); 
$marcus = str_replace('ع','عٌ',$marcus); 
$marcus = str_replace('ه','هِ',$marcus); 
$marcus = str_replace('خ','خ᪳᪲',$marcus); 
$marcus = str_replace('ح','ح᪽',$marcus); 
$marcus = str_replace('ج','ج᪷᪹',$marcus); 
$marcus = str_replace('ش','شٍ',$marcus); 
$marcus = str_replace('س',' َّس',$marcus); 
$marcus = str_replace('ي','ي᪸᪰',$marcus); 
$marcus = str_replace('ب','بᤠ',$marcus);
$marcus = str_replace('ل','لཻ',$marcus); 
$marcus = str_replace('ا','اི',$marcus); 
$marcus = str_replace('ت','تّ',$marcus); 
$marcus = str_replace('ن','ن྄༹',$marcus); 
$marcus = str_replace('ك','كิ',$marcus); 
$marcus = str_replace('م','مຼ',$marcus); 
$marcus = str_replace('ة',' ةّ',$marcus); 
$marcus = str_replace('ء','ء',$marcus); 
$marcus = str_replace('ظ','ظܱܰ',$marcus); 
$marcus = str_replace('ط','ط์',$marcus); 
$marcus = str_replace('ذ','ذٍُ',$marcus); 
$marcus = str_replace('د','دّ',$marcus); 
$marcus = str_replace('ز','زٌِ',$marcus); 
$marcus = str_replace('ر','رٰ',$marcus); 
$marcus = str_replace('و','وٰ໑ٰ',$marcus); 
$marcus = str_replace('ى','ىّ',$marcus);
bot('sendMessage',[ 
'chat_id'=>$chatId,
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('ض','ضُ',$messageText);
$marcus = str_replace('ص','صّ',$marcus); 
$marcus = str_replace('ث','ثُ',$marcus); 
$marcus = str_replace('ق','قً',$marcus); 
$marcus = str_replace('ف','فَ',$marcus); 
$marcus = str_replace('غ','غً',$marcus); 
$marcus = str_replace('ع','ْع ',$marcus); 
$marcus = str_replace('ه','هہ',$marcus); 
$marcus = str_replace('خ','خہ',$marcus); 
$marcus = str_replace('ح','حہ',$marcus); 
$marcus = str_replace('ج','جہ',$marcus); 
$marcus = str_replace('ش','شہ',$marcus); 
$marcus = str_replace('س',' سہ',$marcus); 
$marcus = str_replace('ي','يہ',$marcus); 
$marcus = str_replace('ب','بہ',$marcus);
$marcus = str_replace('ل','لَ',$marcus); 
$marcus = str_replace('ا','آ',$marcus); 
$marcus = str_replace('ت','تہ',$marcus); 
$marcus = str_replace('ن','نہ',$marcus); 
$marcus = str_replace('ك','كہ',$marcus); 
$marcus = str_replace('م','مہ',$marcus); 
$marcus = str_replace('ة',' ةّ',$marcus); 
$marcus = str_replace('ء','ء',$marcus); 
$marcus = str_replace('ظ','ظہ',$marcus); 
$marcus = str_replace('ط','طہ',$marcus); 
$marcus = str_replace('ذ','ذّ',$marcus); 
$marcus = str_replace('د','دِ',$marcus); 
$marcus = str_replace('ز','زِّ',$marcus); 
$marcus = str_replace('ر','ڒٍ',$marcus); 
$marcus = str_replace('و','وِ',$marcus); 
$marcus = str_replace('ى','ىّ',$marcus);
bot('sendMessage',[ 
'chat_id'=>$chatId,
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('ض','᎗ᘞ̇',$messageText);
$marcus = str_replace('ص',' ᎗ᘗ',$marcus); 
$marcus = str_replace('ث','᎗̇̈ɹ ',$marcus); 
$marcus = str_replace('ق','',$marcus); 
$marcus = str_replace('ف','',$marcus); 
$marcus = str_replace('غ','᎗ϛ',$marcus); 
$marcus = str_replace('ع','᎗ჺ',$marcus); 
$marcus = str_replace('ه','᎗බ',$marcus); 
$marcus = str_replace('خ','ᓘ',$marcus); 
$marcus = str_replace('ح','ᓗ',$marcus); 
$marcus = str_replace('ج','ᓗฺ',$marcus); 
$marcus = str_replace('س',' ᎗ɹɹɹ',$marcus); 
$marcus = str_replace('ش','᎗ɹ̇̈ɹɹ',$marcus); 
$marcus = str_replace('ي',' ᎗̤ɹ',$marcus); 
$marcus = str_replace('ب','᎗̣ɹ ',$marcus);
$marcus = str_replace('ل','⅃',$marcus); 
$marcus = str_replace('ا','Ȋ',$marcus); 
$marcus = str_replace('ت','᎗̈ɹ',$marcus); 
$marcus = str_replace('ن','᎗̇ɹ',$marcus); 
$marcus = str_replace('ك','ܭ',$marcus); 
$marcus = str_replace('م','ᓄ',$marcus); 
$marcus = str_replace('ة',' ᎗Ꭷ',$marcus); 
$marcus = str_replace('ء','ء',$marcus); 
$marcus = str_replace('ظ','᎗̇Ь',$marcus); 
$marcus = str_replace('ط','᎗Ь',$marcus); 
$marcus = str_replace('ذ','ذّ',$marcus); 
$marcus = str_replace('د','ↄ',$marcus); 
$marcus = str_replace('ز','j',$marcus); 
$marcus = str_replace('ر','ڒٍ',$marcus); 
$marcus = str_replace('و','g',$marcus); 
$marcus = str_replace('ى','ىّ',$marcus);
bot('sendMessage',[ 
'chat_id'=>$chatId,
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
]);
$items = ['𝄮' , '𝄵' , '𓃠' , 'ま' , '⚚' , '†' , '⁦♡⁩' , '⁦˖꒰' , '⁦ਊ' , '❥' , '⁦㉨' , '𝆹𝅥𝅮' , '𝄬' , '𝄋' , '𖤍' , '𖠛' , ' 𝅘𝅥𝅮' , '⁦♬⁩' , '⁦⁦ㇱ'  , '⁦☊' , '𝅘𝅥𝅯' , 'メ',]; 
$_smile = array_rand($items,1);
$smile = $items[$_smile];
$count = count($messageText);
$marcus = str_replace('ض','ضـٰ๋۪͜ﮧٰ',$messageText);
$marcus = str_replace('ص','صـٌٍ๋ۤ͜ﮧْ',$marcus); 
$marcus = str_replace('ث','ث̲ꫭـﮧ',$marcus); 
$marcus = str_replace('ق','قٰٰྀ̲ـِٰ̲ﮧْ',$marcus); 
$marcus = str_replace('ف','فـٌٍ๋ۤ͜ﮧ',$marcus); 
$marcus = str_replace('غ','غـّٰ̐ہٰٰ',$marcus); 
$marcus = str_replace('ع','عٌ',$marcus); 
$marcus = str_replace('ه','ھہ',$marcus); 
$marcus = str_replace('خ','خ̲ﮧ',$marcus); 
$marcus = str_replace('ح','ح̲ꪳـﮧ',$marcus); 
$marcus = str_replace('ج','ج̲ꪸـﮧ',$marcus); 
$marcus = str_replace('ش','ش̲ꪾـﮧ',$marcus); 
$marcus = str_replace('س','سـ̷ٰٰﮧْ',$marcus); 
$marcus = str_replace('ي','يـِٰ̲ﮧ',$marcus); 
$marcus = str_replace('ب','ب̲ꪰـﮧْ',$marcus);
$marcus = str_replace('ل','لٍُـّٰ̐ہ',$marcus); 
$marcus = str_replace('ا','أّ',$marcus); 
$marcus = str_replace('ت','تـٰۧﮧ',$marcus); 
$marcus = str_replace('ن','نٰ̲̐ـﮧْ',$marcus); 
$marcus = str_replace('ك','كـِّﮧْٰٖ',$marcus); 
$marcus = str_replace('م','مٰٰྀ̲ـِٰ̲ﮧْ',$marcus); 
$marcus = str_replace('ة',' ةً',$marcus); 
$marcus = str_replace('ء','ء',$marcus); 
$marcus = str_replace('ظ','ظَـ๋͜ﮧْ',$marcus); 
$marcus = str_replace('ط','ط̲꫁ـﮧ',$marcus); 
$marcus = str_replace('ذ','ذٖ',$marcus); 
$marcus = str_replace('د','دُ',$marcus); 
$marcus = str_replace('ز','ژٰ',$marcus); 
$marcus = str_replace('ر','ڒٍ',$marcus); 
$marcus = str_replace('و','ﯛ૭',$marcus); 
$marcus = str_replace('ى','ىّ',$marcus);
bot('sendMessage',[ 
'chat_id'=>$chatId,
'text'=>''.$marcus.' '.$smile.'',
'parse_mode'=>'MarkDown',
]);
 bot('sendMessage',[
'chat_id'=>$chatId,
'text'=>"*
• تم زخرفة الاسم $messageText
• ويمكنك رؤية الرموز ايضاً بالضغط على رموز 
• يرجى الضغط على زر عوده في الاسفل
*"
,'parse_mode'=>"markdown",'disable_web_page_preview'=>true,'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'الرموز' ,'callback_data'=>"at"],['text'=>'رجوع' ,'callback_data'=>"home"]],
[['text'=>'زخرفة مرة اخرى ✹' ,'callback_data'=>"ii"]],
]])
]);
unset($using[$chatId]);
file_put_contents("using.json",json_encode($using,128|32|256));
}
} 
if($data == "enasm" ){
bot('EditMessageText',[
'chat_id'=>$chatId,
'message_id'=>$messageId,
'text'=>"
*- 𝗦𝗔𝗥𝗔 𝟐𝟎𝟐𝟏 🎄꙳.

- 𝗙𝗢𝗙𝗔 𝟐𝟎𝟐𝟏 🎄꙳.

- 𝗠𝗘𝗠𝗘 𝟐𝟎𝟐𝟏 🎄꙳.

- 𝗦𝗢𝗦𝗢 𝟐𝟎𝟐𝟏 🎄꙳.

- 𝗕𝗔𝗡𝗢 𝟐𝟎𝟐𝟏 🎄꙳.

- 𝗡𝗢𝗢𝗥 𝟐𝟎𝟐𝟏 🎄꙳.

𓆩𝗭𝗮𝗶𝗻𝗮𝗯𓆪  ˹🎄˼ .

𓆩𝗦𝗷𝗮𝗮𓆪  ˹🎄˼ .

𓆩𝗔𝘆𝗮𓆪  ˹🎄˼ .

𓆩𝗔𝘀𝗿𝗮𝗮𓆪  ˹🎄˼ .

𓆩𝗧𝗮𝗯𝗮𝗿𝗸𓆪  ˹🎄˼ .

-  𝑜𝑡ℎ𝑚𝑎𝑛 🌵.

-  𝑂𝑚𝑒𝑟 🌵 .

-  𝑎𝑙𝑖 🌵 .

-  𝑡𝑜𝑚𝑎 🌵 .

𖥻 𝙅𝙖𝙣𝙖𝙩 🍇.

𖥻 𝙁𝙖𝙩𝙚𝙢𝙖 🍇.

𖥻 𝙕𝙖𝙮𝙣𝙖𝙗 🍇.

𖥻 𝙍𝙚𝙚𝙢 🍇.

. 𝗵𝘀𝘀𝗮𝗻 🦚.

• ّ𝘀𝗼𝗸𝗮𝗿 🦚.

، 𝗖𝗔𝗞𝗘 🌳.
————————*
",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=>true,
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[['text'=>'رجوع' ,'callback_data'=>"home"]],
]])
]); 
}
if($data == "arasm" ){
bot('EditMessageText',[
'chat_id'=>$chatId,
'message_id'=>$messageId,
'text'=>"
*- ؏َـثمانَ 🍇.

- ؏ـمرَ  🍇.

- ؏َـلييہَ 🍇.

- تو͡୭ما 🍇.

- تــﯢت𝟐𝟎𝟐𝟏 🎄꙳.

- شَيـטּ 𝟐𝟎𝟐𝟏 🎄꙳.

- نــﯢטּ 𝟐𝟎𝟐𝟏 🎄꙳.

- مَيممہَ 𝟐𝟎𝟐𝟏 🎄꙳.

- ݽيـטּ 𝟐𝟎𝟐𝟏 🎄꙳.

- دنــ͚͆ـو 𝟐𝟎𝟐𝟏 🎄꙳.

- ۿهـَدى 💕.

- سمــَࢪ 💕.

————————*
",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=>true,
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[['text'=>'رجوع' ,'callback_data'=>"home"]],
]])
]); 
}
if($data == "cgasm" ){
bot('EditMessageText',[
'chat_id'=>$chatId,
'message_id'=>$messageId,
'text'=>"
𝙁𝙍𝘼𝙉𝘾𝙊𝙄𝙎 𝟐𝟎𝟐𝟏 🎄꙳.
𝙆𝙀𝙑𝙄𝙉 𝟐𝟎𝟐𝟏 🎄꙳.
𝘼𝙉𝙇𝙊 𝟐𝟎𝟐𝟏 🎄꙳.
𝘾𝙃𝙄𝙏𝙏𝙊 𝟐𝟎𝟐𝟏 🎄꙳.
𝙎𝘼𝙑𝙊 𝟐𝟎𝟐𝟏 🎄꙳.
𝘾𝙃𝙄𝘾𝙊 𝟐𝟎𝟐𝟏 🎄꙳.
𝘾𝙃𝙄𝘾𝘼𝙂𝙊 𝟐𝟎𝟐𝟏 🎄꙳.
𝙀𝘾𝙃𝙊 𝟐𝟎𝟐𝟏 🎄꙳.
𝙔𝘼𝙔𝙊 𝟐𝟎𝟐𝟏 🎄꙳.
𝙈𝘼𝙍𝘾𝙀𝙑𝙊 𝟐𝟎𝟐𝟏 🎄꙳.
𝙔𝙄𝙎𝙆𝘼 𝟐𝟎𝟐𝟏 🎄꙳.
————————
𝐌𝐈𝐋𝐀𝐍 🌵
𝐀𝐍𝐈𝐒𝐀𝐔 🌵
𝐅𝐑𝐀𝐍𝐂𝐈𝐒𝐎 🌵
𝐀𝐏𝐑𝐈𝐋  🌵
————————
𝙛𝙞𝙘𝙤 🎄
𝙞𝙨𝙝𝙤 🎄
𝙖𝙗𝙧𝙖𝙨 🎄
𝙣𝙞𝙣𝙤 🎄
————————
..⌠𝐒𝐞𝐥𝐯𝐚𝐧𝐚⌡𓊑.
..⌠𝐓𝐨𝐛𝐚𝐤⌡𓊑.
..⌠𝐄𝐥𝐤𝐚𝐫⌡𓊑.
..⌠𝐌𝐚𝐲𝐚⌡𓊑.
..⌠𝐓𝐞𝐨𝐨⌡𓊑.
..⌠𝐌𝐞𝐚⌡𓊑.
..⌠𝐋𝐞𝐥𝐞⌡𓊑.
————————
⌯ ˹𝙆𝙖𝙧𝙖˼ 
⌯ ˹𝙉𝙖??𝙧˼ 
⌯ ˹𝙂𝙢𝙧˼ 
⌯ ˹𝘿𝙚𝙫˼ 
⌯ ˹𝙀𝙫𝙖˼
————————
: ˹𝘾𝘼𝙍𝙊𝙇𝙄𝙉𝙀˼ 𓄧 .
: ˹𝘾𝙍𝙔𝙎𝙏𝘼𝙇˼ 𓄧 .
: ˹𝙇𝘼𝙐𝙍𝙀𝙉˼ 𓄧 .
: ˹𝙆𝘼𝙈𝙄𝙇𝘼˼ 𓄧 .
: ˹𝘿𝘼𝙉𝘼˼ 𓄧 .
: ˹𝙍𝙄𝙏𝘼˼ 𓄧 .
: ...................
",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=>true,
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[['text'=>'رجوع' ,'callback_data'=>"home"]],
]])
]); 
}
if($data == "dates" ){
bot('EditMessageText',[
'chat_id'=>$chatId,
'message_id'=>$messageId,
'text'=>"
————————
• 𝟏𝟗𝟗𝟎
• 𝟏𝟗𝟗𝟏
• 𝟏𝟗𝟗𝟐
• 𝟏𝟗𝟗𝟑
• 𝟏𝟗𝟗𝟒
• 𝟏𝟗𝟗𝟓
• 𝟏𝟗𝟗𝟔
• 𝟏𝟗𝟗𝟕
• 𝟏𝟗𝟗𝟖
• 𝟏𝟗𝟗𝟗
• 𝟐𝟎𝟎𝟎
• 𝟐𝟎𝟎𝟏
• 𝟐𝟎𝟎𝟐
• 𝟐𝟎𝟎𝟑
• 𝟐𝟎𝟎𝟒
• 𝟐𝟎𝟎𝟓
• 𝟐𝟎𝟎𝟔
• 𝟐𝟎𝟎𝟕
• 𝟐𝟎𝟎𝟖
• 𝟐𝟎𝟎𝟗
• 𝟐𝟎𝟏𝟎
---------------
• 𝒋𝒂𝒏𝒖𝒂𝒓𝒚.
• 𝒇𝒆𝒃𝒓𝒖𝒂𝒓𝒚.
• 𝒎𝒂𝒓𝒄𝒉.
• 𝒂𝒑𝒓𝒊𝒍.
• 𝒎𝒂𝒚.
• 𝒋𝒖𝒏𝒆.
• 𝒋𝒖𝒍𝒚.
• 𝒂𝒖𝒈𝒖𝒔𝒕.
• 𝒔𝒆𝒑𝒕𝒆𝒎𝒃𝒆𝒓.
• 𝒐𝒄𝒕𝒐𝒃𝒆𝒓.
• 𝒏𝒐𝒗𝒆𝒎𝒃𝒆𝒓.
• 𝒅𝒆𝒄𝒆𝒎𝒃𝒆𝒓.
--------------
• 𝐒𝐔𝐍𝐃𝐀𝐘.♡
• 𝐌𝐎𝐍𝐃𝐀𝐘.♡
• 𝐓𝐔𝐄𝐒𝐃𝐀𝐘.♡
• 𝐖𝐄𝐃𝐍𝐄𝐒𝐃𝐀𝐘.♡
• 𝐓𝐇𝐔𝐑𝐒𝐃𝐀𝐘.♡
• 𝐅𝐑𝐈𝐃𝐀𝐘.♡
• 𝐒𝐀𝐓𝐔𝐑𝐃𝐀𝐘.♡
————————
",
'parse_mode'=>'MarkDown',
'disable_web_page_preview'=>true,
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[['text'=>'رجوع' ,'callback_data'=>"home"]],
]])
]); 
}
