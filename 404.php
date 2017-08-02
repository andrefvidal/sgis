<?php
	require_once("head.html");
	//error_reporting(0);
?>



<!-- error section -->
<section class="page404">
<div class="container">
<div class="row">
<div class="col-xs-12 text-center">
<div class="text404">404</div>
<p>A página que você está procurando não pode ser encontrada.</p>
<div class="btn-block-404 m-t-1-404"> 
<a href="<?php 	if(isset($_SERVER['HTTP_REFERER'])) {
				echo $_SERVER['HTTP_REFERER'];
				}else{echo "/";};?>" class="btn404 btn-primary404 btn-lg404">Voltar para página anterior.</a>
</div>
</div>
</div>
</div>
</section>


<?php
	require_once("footer.html");
	//error_reporting(0);
?>