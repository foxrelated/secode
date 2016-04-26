<h2>Spam Control</h2>
<div class="tabs">
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>	
</div>
<div>
    <div class="admin_search" style="margin-bottom: 10px; ">
         <?php echo $this->form->render($this); ?>
     </div>
    
    <div>
      <span style="font-weight: bold; font-size: 14px">If you want more security from spam then use Recaptcha in Se4.</span><br>
      <span>The steps of implementation are...</span><br>
      <span>1. Remember: You must have to enable captcha for signup from admin>>settings>>spam and banning tools.</span><br>
      <span>2.  Goto <a href="https://www.google.com/recaptcha/admin/create" target="_blank">https://www.google.c...ha/admin/create</a> & register your site URL there<span><br>
      <span>3. It will give you a public & a private key. Save that keys. In the form above;</span><br>
      <span>4. Open file with any ftp manager</span><br>
      <div class="tip">
        <span>application/modules/User/Form/Signup/Account.php</span>
      </div>
      <span>5. Find line(260)</span><br>
      <div class="tip">
          <span>
              if (Engine_Api::_()-&gt;getApi('settings', 'core')-&gt;core_spam_signup) <br> {<br>&nbsp; &nbsp; &nbsp;$this-&gt;addElement('captcha', 'captcha', array( <br>&nbsp; &nbsp; &nbsp;'description' =&gt; '_CAPTCHA_DESCRIPTION', <br>&nbsp; &nbsp; &nbsp;'captcha' =&gt; 'image', <br>&nbsp; &nbsp; &nbsp;'required' =&gt; true, <br>&nbsp; &nbsp; &nbsp;'allowEmpty' =&gt; false, <br>&nbsp; &nbsp; &nbsp;'captchaOptions' =&gt; array( <br>&nbsp; &nbsp; &nbsp;'wordLen' =&gt; 6, <br>&nbsp; &nbsp; &nbsp;'fontSize' =&gt; '30', <br>&nbsp; &nbsp; &nbsp;'timeout' =&gt; 300, <br>&nbsp; &nbsp; &nbsp;'imgDir' =&gt; APPLICATION_PATH . '/public/temporary/', <br>&nbsp; &nbsp; &nbsp;'imgUrl' =&gt; $this-&gt;getView()-&gt;baseUrl().'/public/temporary', <br>&nbsp; &nbsp; &nbsp;'font' =&gt; APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf' <br>&nbsp; &nbsp; &nbsp; )<br>&nbsp; &nbsp;)); <br> }
          </span>    
              </div>
      <span>6. Replace it with</span>
      <div class="tip">
      <span>$privatekey = Engine_Api::_()->getApi('settings', 'core')->getSetting('spamcontrol.privatekey', '');<br>
  $publickey = Engine_Api::_()->getApi('settings', 'core')->getSetting('spamcontrol.publickey', '');<br>  
  if (Engine_Api::_()-&gt;getApi('settings', 'core')-&gt;core_spam_signup)<br> { <br>&nbsp; &nbsp; &nbsp;$this-&gt;addElement('captcha', 'captcha', array( <br>&nbsp; &nbsp; &nbsp;'description' =&gt; '_CAPTCHA_DESCRIPTION',<br>&nbsp; &nbsp; &nbsp;'captcha' =&gt; 'reCaptcha', <br>&nbsp; &nbsp; &nbsp;'required' =&gt; true,<br>&nbsp; &nbsp; &nbsp;'allowEmpty' =&gt; false, <br>&nbsp; &nbsp; &nbsp;'captchaOptions' =&gt; array( <br>&nbsp; &nbsp; &nbsp;'pubKey' =&gt; $publickey, <br>&nbsp; &nbsp; &nbsp;'privKey' =&gt; $privatekey, <br>&nbsp; &nbsp; &nbsp;'wordLen' =&gt; 6,<br>&nbsp; &nbsp; &nbsp;'fontSize' =&gt; '30', <br>&nbsp; &nbsp; &nbsp;'timeout' =&gt; 300, <br>&nbsp; &nbsp; &nbsp;'imgDir' =&gt; APPLICATION_PATH . '/public/temporary/',<br>&nbsp; &nbsp; &nbsp;'imgUrl' =&gt; $this-&gt;getView()-&gt;baseUrl().'/public/temporary', <br>&nbsp; &nbsp; &nbsp;'font' =&gt; APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf' <br>&nbsp; &nbsp; )&nbsp;<br>&nbsp; )); <br> }</span>
      </div>
    <span>7. Save & upload the modified file.</span><br>

<span>Thats it...</span>
   
</div>    
