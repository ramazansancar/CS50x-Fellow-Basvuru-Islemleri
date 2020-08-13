<?php
error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set('Europe/Istanbul');

########################################################################
################ DÜZENLEMENİZ##GEREKEN ALANLAR #########################
##################### BURADA BAŞLIYOR ##################################
########################################################################
define("SITEBASLIK","Site Başlığı"); // Site Başlığını buradan Tanımlayınız
########################################################################
########################################################################
################ MAİL AYARLARI BURADA YER ALIYOR #######################
define("SMTP_SERVER", "mail.siteniz.com"); // mail.alanadiniz.com
define("SMTP_PORT", "465"); // 465
define("SMTP_USER", "info@siteniz.com"); // ornek@alanadiniz.com
define("SMTP_PASS", "***********"); // **********
define("MAIL_GONDEREN", "[XXX Sistem]");
################### Mail Yollarken Değişmesini istedikleriniz. #########
$mail_basligi = "XXXXXXXXXX Çalışma Grubu Başvurusu";
$mail_html = "mail/yolla.html";
########################################################################
##### Burada yer alan sabit değişkenler Mail içinde [XXX] şeklinde #####
########### belirtilen kelimelerin değiştirilmesini sağlar. ############
########################################################################
$sabit_degistir = array(
	'images/' => 'https://ramazansancar.com.tr/cs50/images/', // "mail/images/" klasörünün içindeki göresellerin yer aldığı web sitesini giriniz.
	'[GRUBA KATIL]' => 'https://discord.gg/ABCDEFG', // Fellow Kendi Grubu Davet Bağlantısı
	'[FELLOWFACEBOOK]' => 'https://www.facebook.com/ramazansancar', // Fellow Facebook
	'[FELLOWTWITTER]' => 'https://twitter.com/sancaramazan', // Fellow Twitter
	'[FELLOWINSTAGRAM]' => 'https://www.instagram.com/sancaramazan/', // Fellow Instagram
	'[FELLOWLINKEDIN]' => 'https://www.linkedin.com/in/ramazansancar/', // Fellow Linkedin
	'[FELLOWTELEGRAM]' => 'https://t.me/sancaramazan', // Fellow Telegram
	'[FELLOW]' => 'Ramazan Sancar', // Fellow Ad Soyad
	'[FELLOWMAIL]' => 'me@ramazansancar.com.tr' // Fellow Mail
);
################ VERİTABANI AYARLARI BURADA YER ALIYOR #################
define('DATABASE_HOST', 'localhost'); // Veritabanı Server İP Adresiniz
define('DATABASE_USER', 'root'); // Veritabanı Kullanıcı adınız
define('DATABASE_PASS', ''); // Veritabanı Şifreniz
define('DATABASE_NAME', 'xxxxxxxx'); // Veritabanı adı
############## EXCEL AYARLARI BURADA YER ALIYOR ########################
$inputFileType = 'Excel2007'; // .xlsx için Excel2007 | .xls için Excel5
$inputFileName = 'Form_verileri.xlsx'; // İçe Aktarılacak Dosya
$sheetname = 'Sayfa1'; // Katman Adı
########################################################################
########################### ARTIK KULLANMAYA ###########################
############################ BAŞLAYABİLİRSİN ###########################
####### ALT KISMI SADECE BİLEN KİŞİLERİN DÜZENLEMESİ DAHA İYİ OLUR #####
########################################################################

require_once("Sistem/basicdb.php");
require_once("Sistem/class.phpmailer.php");
$db = new BasicDB(DATABASE_HOST, DATABASE_NAME, DATABASE_USER, DATABASE_PASS);


/****************************\
|****************************|
|******Fonksiyonlar**********|
|****************************|
\****************************/
function OnEk(){
	echo 'Lütfen Yapmak istediğiniz işlemi seçiniz.</br><h3><a href="?islem=kayit">Veritabanı Kayıt Et</a><font style="color:red">*</font> | <a href="?islem=vtoku">Veritabanı Oku</a> | <a href="?islem=oku">Excel Oku</a> | <a href="?islem=mail">Mail Gönder</a><font style="color:red">*</font></h3><font style="color:red">*</font> Tıklandığı taktirde direkt işlem yapmaktadır. Lüften dikkatli olun.';
}

// Belli karakterler ile kelime ayırma
function multiexplode ($delimiters,$string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}
// Kelime Değiştirme
function DegistirmeFonksiyonu(array $deger, $kaynak) {
   return str_replace(array_keys($deger), array_values($deger), $kaynak);   
}

// Mail Gönderme
function MailGonder($alici,$alici_isim,$mail_baslik,$mail_icerik,$sunucu = SMTP_SERVER,$port = SMTP_PORT,$kullanici = SMTP_USER,$sifre = SMTP_PASS,$mail_gonderen = MAIL_GONDEREN){
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPDebug = 0;
	$mail->SMTPAuth = true;
	$mail->Host = $sunucu;
	$mail->Port = $port;
	$mail->SMTPSecure = 'ssl';
	$mail->Username = $kullanici;
	$mail->Password = $sifre;
	$mail->SetLanguage("tr","");
	$mail->SetFrom($mail->Username, $mail_gonderen);
	$mail->AddAddress($alici, $alici_isim);
	$mail->CharSet = 'UTF-8';
	$mail->Subject = $mail_baslik;
	$content = '<div style="background: #eee; padding: 10px; font-size: 14px">'.$mail_icerik.'</div>';
	$mail->MsgHTML($content);
	if($mail->Send()) {
		// e-posta başarılı ile gönderildi
		$sonuc = "katılımcısına mail başarıyla gönderildi.";
	} else {
		// bir sorun var, sorunu ekrana bastıralım
		$sonuc = '<font style="color:red">Mail Gönderilemedi. Gönderim Hatası: <b>' . $mail->ErrorInfo.'</b></font>';
	}
	return $sonuc;
}

function Oku($inputFileType,$inputFileName,$sheetname){
	/*##########################\
	|######Excel Ayarları#######|
	\##########################*/

	/** Include path **/
	set_include_path(get_include_path() . PATH_SEPARATOR . './Classes/');
	/** PHPExcel_IOFactory */
	include 'PHPExcel/IOFactory.php';

	echo 'Açılan Dosya : ',pathinfo($inputFileName,PATHINFO_BASENAME),' | Kullanılan Excel Türü : ',$inputFileType,'<br />';
	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
	echo 'Yüklenen Katman : "',$sheetname,'"<br />';
	$objReader->setLoadSheetsOnly($sheetname);
	$objPHPExcel = $objReader->load($inputFileName);
	echo '<hr />';
	$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

	echo "<table><tr><td><b>ID</b></td><td><b>Kayıt Tarihi</b></td><td><b>E-posta</b></td><td><b>İsim - Soyisim</b></td><td><b>Yaşadığın Şehir</b></td><td><b>Cinsiyet</b></td><td><b>Telefon Numaranız</b></td><td><b>Doğum Tarihiniz</b></td><td><b>Eğitim ve/veya Kariyeriniz</b></td><td><b>Kendinden biraz bahseder misin ?</b></td>";
	for ($i=2; $i <= count($sheetData); $i++) {
	echo "<tr><td>";
		// Hakkkında Kısmı boş ise;
		if( empty($sheetData[$i]["I"]) ){
			$sheetData[$i]["I"] = "";
		}
		echo ($i-1); // Kayıt Tarihi
		echo "</td><td>";
		echo $sheetData[$i]["A"]; // Kayıt Tarihi
		echo "</td><td>";
		echo $sheetData[$i]["B"]; // E-posta
		echo "</td><td>";
		echo $sheetData[$i]["C"]; // İsim - Soyisim
		echo "</td><td>";
		echo $sheetData[$i]["D"]; // Yaşadığın Şehir
		echo "</td><td>";
		echo $sheetData[$i]["E"]; // Cinsiyet
		echo "</td><td>";
		echo $sheetData[$i]["F"]; // Telefon Numaranız
		echo "</td><td>";
		echo $sheetData[$i]["G"]; // Doğum Tarihiniz
		echo "</td><td>";
		echo $sheetData[$i]["H"]; // Eğitim ve/veya Kariyeriniz
		echo "</td><td>";
		echo $sheetData[$i]["I"]; // Kendinden biraz bahseder misin ?
		echo "</td><tr>";
	}
	echo "<table>";
	echo "<b>Toplam ".($i-2)." katılımcı listelendi.<b>";
}

function Kayit($inputFileType,$inputFileName,$sheetname){
global $db;
	/*##########################\
	|######Excel Ayarları#######|
	\##########################*/

	/** Include path **/
	set_include_path(get_include_path() . PATH_SEPARATOR . './Classes/');
	/** PHPExcel_IOFactory */
	include 'PHPExcel/IOFactory.php';

	echo 'Açılan Dosya : ',pathinfo($inputFileName,PATHINFO_BASENAME),' | Kullanılan Excel Türü : ',$inputFileType,'<br />';
	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
	echo 'Yüklenen Katman : "',$sheetname,'"<br />';
	$objReader->setLoadSheetsOnly($sheetname);
	$objPHPExcel = $objReader->load($inputFileName);
	echo '<hr />';
	$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

	for ($i=2; $i <= count($sheetData); $i++) {
		// Hakkkında Kısmı boş ise Kodun Hata vermesini Önleme;
		if( empty($sheetData[$i]["I"]) ){
			$sheetData[$i]["I"] = "";
		}

		// Günleri ve Saati Veritabanı yapısına döüştürme
		list($kayit_gun, $kayit_ay, $kayit_yil, $kayit_saat, $kayit_dakika, $kayit_saniye) = multiexplode(array("/"," ",":"), $sheetData[$i]["A"]);
		list($dogum_ay, $dogum_gun, $dogum_yil) = multiexplode(array("/"), $sheetData[$i]["G"]);
		$sheetData[$i]["A"] = $kayit_yil."-".$kayit_ay."-".$kayit_gun." ".$kayit_saat.":".$kayit_dakika.":".$kayit_saniye;
		$sheetData[$i]["G"] =  $dogum_yil."-".$dogum_ay."-".$dogum_gun;

		$kayitet = $db->insert('katilimcilar')->set(array(
		'uye_tarih' => $sheetData[$i]["A"], // Kayıt Tarihi
		'uye_mail' => $sheetData[$i]["B"], // E-posta
		'uye_adsoyad' => $sheetData[$i]["C"], // İsim - Soyisim
		'uye_sehir' => $sheetData[$i]["D"], // Yaşadığın Şehir
		'uye_cinsiyet' => $sheetData[$i]["E"], // Cinsiyet
		'uye_telefon' => $sheetData[$i]["F"], // Telefon Numaranız
		'uye_dogum_tarihi' => $sheetData[$i]["G"], // Doğum Tarihiniz
		'uye_kariyer_okul' => $sheetData[$i]["H"], // Eğitim ve/veya Kariyeriniz
		'uye_aciklama' => $sheetData[$i]["I"] // Kendinden biraz bahseder misin ?
		));
		if($kayitet){
			echo "#".($i-1)." Sisteme kayıt olma başarılı bir şekilde yapılmıştır. (".$sheetData[$i]["C"].")</br>";
		}else{
			echo "#".($i-1)." Sisteme kayıt olma sırasında hata oluştu. (".$sheetData[$i]["C"].")</br>";
		}

		if($i == count($sheetData) ){
			echo "<b>".($i-1)." Katılımcı Veritanına kayıt edildi.</b>";
		}
	}
}

function VtOku(){
global $db;
$tum_katilimcilar = $db->from("katilimcilar")->all();
$katilimci_sayisi = $db->from("katilimcilar")->select('count(uye_id) as total')->total();

if ( $tum_katilimcilar ){
	echo "<table><tr><b><td>ID</td></b><td><b>Kayıt Tarihi</b></td><td><b>E-posta</b></td><td><b>İsim - Soyisim</b></td><td><b>Yaşadığın Şehir</b></td><td><b>Cinsiyet</b></td><td><b>Telefon Numaranız</b></td><td><b>Doğum Tarihiniz</b></td><td><b>Eğitim ve/veya Kariyeriniz</b></td><td><b>Kendinden biraz bahseder misin ?</b></td>";
  foreach ( $tum_katilimcilar as $katilimci ){
	  	$uye_id = $katilimci["uye_id"];
	  	$uye_tarih = $katilimci["uye_tarih"];
	  	$uye_mail = $katilimci["uye_mail"];
		$uye_adsoyad = $katilimci["uye_adsoyad"];
	  	$uye_sehir = $katilimci["uye_sehir"];
	  	$uye_cinsiyet = $katilimci["uye_cinsiyet"];
	  	$uye_telefon = $katilimci["uye_telefon"];
	  	$uye_dogum_tarihi = $katilimci["uye_dogum_tarihi"];
	  	$uye_kariyer_okul = $katilimci["uye_kariyer_okul"];
	  	$uye_aciklama = $katilimci["uye_aciklama"];

		echo "<tr><td>";

			echo $uye_id; // Üye ID
			echo "</td><td>";
			echo $uye_tarih; // Kayıt Tarihi
			echo "</td><td>";
			echo $uye_mail; // E-posta
			echo "</td><td>";
			echo $uye_adsoyad; // İsim - Soyisim
			echo "</td><td>";
			echo $uye_sehir; // Yaşadığın Şehir
			echo "</td><td>";
			echo $uye_cinsiyet; // Cinsiyet
			echo "</td><td>";
			echo $uye_telefon; // Telefon Numaranız
			echo "</td><td>";
			echo $uye_dogum_tarihi; // Doğum Tarihiniz
			echo "</td><td>";
			echo $uye_kariyer_okul; // Eğitim ve/veya Kariyeriniz
			echo "</td><td>";
			echo $uye_aciklama; // Kendinden biraz bahseder misin ?
			echo "</td><tr>";
	}
		echo "<table>";
		echo "<b>Toplam ".$katilimci_sayisi." katılımcı listelendi.<b>";
	}else{
	        echo '<font style="color:red;"><h4>Önce Veritabanına <a href="?islem=kayit"> Kayıt </a> Yapman gerekiyor.</h4>';
	}

}

function  MailSayfa($mail_basligi,$sabit_degistir,$mail_html,$SITEBASLIK = SITEBASLIK){
global $db;
$tum_katilimcilar = $db->from("katilimcilar")->all();
$katilimci_sayisi = $db->from("katilimcilar")->select('count(uye_id) as total')->total();

	if ( $tum_katilimcilar ){
	  	foreach ( $tum_katilimcilar as $katilimci ){
		  	$uye_mail = $katilimci["uye_mail"];
			$uye_adsoyad = $katilimci["uye_adsoyad"];

			$icerik = file_get_contents($mail_html);
			$icerik.= '<center><br>Bu mail '.$SITEBASLIK.' <a href="[FELLOWLINKEDIN]">Fellowu</a> tarafından gönderilmiştir.</center>';

			$degistir = array(
				'[ISIM]' => $uye_adsoyad
			);
			$icerik = DegistirmeFonksiyonu( $sabit_degistir, $icerik);
			$icerik = DegistirmeFonksiyonu( $degistir, $icerik);

			$YollaGitsin = "(";
			$YollaGitsin .= $uye_adsoyad.") ";
			$YollaGitsin .= MailGonder($uye_mail,$uye_adsoyad,$mail_basligi,$icerik);
			$YollaGitsin .= "</br>";
			print $YollaGitsin;

			/*
			if($YollaGitsin){
				echo $uye_adsoyad." katılımcısına mail gönderildi.</br>";
			}else{
				echo $uye_adsoyad." Mail Gönderilemedi.</br>";
			}*/
		}
		echo "<b>Toplam ".$katilimci_sayisi." katılımcıya mail gönderildi.<b>";
	}else{
	        echo '<font style="color:red;"><h4>Önce Veritabanına <a href="?islem=kayit"> Kayıt </a> Yapman gerekiyor.</h4>';
	}

}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title><?=SITEBASLIK?></title>
<style type="text/css">
	table{
		background: #ccc;
		color: black;
	}
	tr{
		border: 1px solid black;
	}
	td{
		border: 1px solid black;
	}
</style>
</head>
<body>

<?php
switch (@$_GET["islem"]) {
	case 'kayit':
		OnEk();
		echo "<h1>Kodluyoruz Üyelerini Sisteme Yükleme</h1><h2>Tüm Bilgileri Veritabanına kayıt etme</h2>";
		Kayit($inputFileType,$inputFileName,$sheetname);
		break;
	case 'mail':
		OnEk();
		echo "<h1>Kodluyoruz Üyelerini Mail Gönderme</h1><h2>Tüm Veritabanında yer alan üyelere mail gönderir.</h2><hr>";
		MailSayfa($mail_basligi,$sabit_degistir,$mail_html);
		break;
	case 'oku':
		OnEk();
		echo "<h1>Kodluyoruz Üyelerini Okuma</h1><h2>Tüm Bilgileri Excelden Okuma</h2>";
		Oku($inputFileType,$inputFileName,$sheetname);
		break;
	case 'vtoku':
		OnEk();
		echo "<h1>Kodluyoruz Üyelerini Okuma</h1><h2>Tüm Bilgileri Veritabanından Okuma</h2><hr>";
		VtOku();
		break;
	default:
		OnEk();
		break;
}
?>
<body>
</html>
