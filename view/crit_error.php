<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?= $app_name ?></title>
    <link rel="stylesheet" href="./assets/bootstrap.min.css" />
    <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon"> 
    <style>
    .highlight {padding: 1.5rem;}
    
    .no-scrobars {   
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    </style>
  </head>
  <body>
    <div class="container">
        <main>
            <form method="POST">
                <div class="py-5 text-center">
                    <h2>Looking glass</h2>
		</div>
                <div class="alert alert-danger fade show" role="alert">
                    <?= $app_warn_dlg ?>
		</div>
                <div mt-5>&nbsp;</div>
        </main>
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
            <div>
	    <span class="text-muted"><?= $app_name ?> ( <a href="./about" class="text-muted">About</a> )</span>
            </div>
        </footer>
    </div>
    <script type="text/javascript" src="./assets/bootstrap.bundle.min.js"></script>
  </body>
</html>
