<?php
class PHP_Email_Form {

  public $to;
  public $from_name;
  public $from_email;
  public $subject;
  public $smtp = array();
  public $ajax = false;

  function add_message($content, $label = '') {
    if ($label && $content) {
      return "<strong>$label:</strong> $content<br>";
    } else {
      return $content;
    }
  }

  function send() {
    $message = '';
    foreach ($_POST as $key => $value) {
      if (!is_array($value)) {
        if ($key != 'g-recaptcha-response') {
          $message .= $this->add_message($value, ucfirst($key));
        }
      } else {
        $message .= "<strong>$key:</strong><br>";
        foreach ($value as $item) {
          $message .= (!empty($item)) ? "$item<br>" : '';
        }
      }
    }

    if (isset($_POST['g-recaptcha-response'])) {
      $message .= $this->add_message('reCAPTCHA', 'Captcha');
    }

    $this->subject = isset($this->subject) ? $this->subject : 'Contact Form Submission';
    $this->from_name = isset($this->from_name) ? $this->from_name : 'Contact Form';
    $this->from_email = isset($this->from_email) ? $this->from_email : 'contact@example.com';

    if ($this->ajax) {
      $response = array('success' => false, 'message' => 'Something went wrong, please try again!');

      if (filter_var($this->from_email, FILTER_VALIDATE_EMAIL)) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= "From: {$this->from_name} <{$this->from_email}>" . "\r\n";

        $mail_result = mail($this->to, $this->subject, $message, $headers);
        if ($mail_result) {
          $response['success'] = true;
          $response['message'] = 'Your message has been sent successfully!';
        }
      }
      return json_encode($response);
    } else {
      return 'Form submission must be an AJAX request';
    }
  }
}
?>
