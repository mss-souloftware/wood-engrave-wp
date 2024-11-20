<?php 
/**
 * 
 * author: M. Sufyan Shaikh
 * description: process form and send info to cookie
 * @package Wood Engraving WP
 * @subpackage M. Sufyan Shaikh
 * 
 * 
*/  



function closeprocess(){ ?>
                  <script>
               //   $cookiestatus = setcookie( 'chocol_cookie' , " ", time()-3600); 
                  document.cookie = `chocol_cookie=|; Secure; Max-Age=-35120; path=/`;
                     </script>
 <?php }
                                         
