<?php

/**
 * Class Email
 *
 * Sending emails via SMTP.
 * It uses PHPMailer library to send emails.
 *
 * @package     Rufus
 * @author      Drizzy <hola@drizzy.dev>
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Rufus\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Rufus\Exception\ExceptionHandler;

class Mail
{


    /**
     * @var PHPMailer
     */
    protected static $mail;

     public static  function mail()
    {
        if (!self::$mail instanceof PHPMailer) {
            self::$mail = new PHPMailer(true);

            self::$mail->SMTPDebug  = config('mail.SMTPDebug');
            self::$mail->isSMTP();
            self::$mail->Host       = config('mail.host');
            self::$mail->SMTPAuth   = config('mail.SMTPAuth');
            self::$mail->Username   = config('mail.username');
            self::$mail->Password   = config('mail.password');
            self::$mail->SMTPSecure = config('mail.encryption');
            self::$mail->Port       = config('mail.port');
            self::$mail->CharSet    = config('mail.charset');
            self::$mail->setLanguage(config('mail.language'));
        }
        return self::$mail;
    }

    /**
     * Set E-Mail and Name of Sender
     * @param null $from
     * @param null $name
     */
    public static function from($from = null, $name = null)
    {
        // Recipients
        $from  = ($from  == "" || empty($from)) ? config('mail.from.address') : $from;
        $name =  ($name  == "" || empty($name)) ? config('mail.from.name')    : $name;
        self::mail()->setFrom($from, $name);
        if ($from  != config('mail.from.address')) {
            self::mail()->addReplyTo($from , $name);
        }else if(config('mail.from.replyto') == "" || empty(config('mail.from.replyto'))){

        } else {
            self::mail()->addReplyTo(config('mail.from.replyto'), config('mail.from.name'));
        }
    }

    /**
     * Set E-Mail and Name of Receiver
     * @param $email
     * @param array $name
     */
    public static function to($email, $name = [])
    {
        if (is_array($email))  {
            
            foreach (array_merge($email, $name) as $em => $nm)
            {
                self::mail()->addBCC($em, $nm);                
                
            } 
        }else{
            self::mail()->addAddress($email, $name);
        }       

    }

    /**
     * Set E-Mail and Name of Receiver
     * @param $email
     * @param null $name
     */
    public static function replyto($from, $name = null)
    {
        self::mail()->addReplyTo($from , $name);

    }
    
    /**
     * Set Mail Subject
     * @param $subject
     */
    public static function subject($subject)
    {
        self::mail()->Subject  = $subject;
    }

    /**
     * Set Mail Content
     * @param $message
     */
    public static function message($message, $WordWrap = null)
    {
        self::mail()->isHTML(true);   //Set email format to HTML
        self::mail()->Body = $message;
        self::mail()->WordWrap = $WordWrap; 
        self::mail()->AltBody = 'Su administrador de correo electrónico no es compatible con HTML.';

    }

    /**
     * Set Mail attachments
     * @param $path
     */
    public static function attach($path, $name = [])
    {
    
        if(isset($path)){

            if(is_array($path)){
                foreach(array_merge($path, $name) as $pat => $nm){
                    self::mail()->addAttachment($pat, $nm);         // Add attachments
                }
            }
            else{
                self::mail()->addAttachment($path, $name);         // Add attachments
            }

        }
        
    }

    /**
     * Send Mail
     */
    public static  function send($send = false)
    {


         if ($send === true) {
            if (!self::mail()->send()) throw new ExceptionHandler('Error',self::mail()->ErrorInfo);
            return true;
         }

    }
}