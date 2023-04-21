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
		<?php if( isset($app_warn_dlg) ) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $app_warn_dlg ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
		<?php } // isset( $app_warn_dlg) ?>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label" for="router">Router</label>
			<select id="router" name="router" class="form-select">
			<?php 
				$selected_rtr = $_POST['router'] ?? '';
				if( is_array($rtr_list) ) { foreach($rtr_list as $idx => $val) { 
					$selected = ($selected_rtr == $val) ? ' selected="selected"' : '';
			?>
			<option value="<?= $val ?>" <?= $selected ?>><?= $val ?></option>
			<?php } /*foreach($rtr_list)*/ ; } // is_array($rtr_list) ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="command">Command</label>
			<select id="command" name="command" class="form-select">
			<?php 
				$selected_cmd = $_POST['command'] ?? '';
				if( is_array( $app_commands) ) { foreach($app_commands as $idx => $cmd) {
					$selected = ($selected_cmd == $idx) ? ' selected="selected"' : '';
			?>
			    <option value="<?= $idx ?>"<?= $selected ?>><?= $cmd['display'] ?></option>
			<?php } /*foreach($app_commands)*/ ; } // is_array($app_commands) ?>
                        </select>
                    </div>
                    <div class="col-md-4">
			<label class="form-label" for="arg">Argument</label>
			<?php $arg_val = (!empty($_POST['arg'])) ? " value=\"${_POST['arg']}\"" : ''; ?>
			<input id="arg" name="arg" class="form-control" type="text" placeholder="44.168.x.y" <?= $arg_val ?>></input>
                    </div>
                </div>
                <hr class="my-4" />
                <div class="row">
                    <div class="col-md-2 offset-md-8 mb-3">
                        <button type="reset"  class="w-100 btn btn-outline-secondary btn-lg">Reset</button>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="w-100 btn btn-primary btn-lg">Submit</button>
                    </div>
                </div>
	    </form>
            <?php if( isset($app_output) ) { ?>
            <div class="text-white bg-dark highlight mt-5">
	        <pre class="no-scrobars mb-0"><code><?= $app_output ?></code></pre>
	    </div>
            <?php } // isset($app_output); ?>
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
