<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/17
 * Time: 17:32
 */

namespace app\Logic;


/**
 * 生成二维码
 * Class Qrcode
 * @package app\Logic
 */
class Qrcodes {

    /**
     * phpqrcode生成二维码获取方法
     * @param $url 调转地址
     */
    public static function getQrcode($url){
        include_once(str_replace('\\','/',app_path()) .'/Http/Qrcode/qrcode.php');
//        return \QRcode::png(urldecode(htmlspecialchars_decode($url)),false,'H',10,10);
//        exit();
        \QRcode::png($url, 'login.png', 'H', 6, 2);
        $logo = public_path('admin/images/bg.png');//准备好的logo图片
        $QR   = 'login.png';//已经生成的原始二维码图
        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }
        imagepng($QR, 'login_temp.png');
        return 'login.png';
//        return '<img src="login.png">';
    }
}