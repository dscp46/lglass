<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?= $app_name ?> - About</title>
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
                <div class="py-5 text-center">
                    <h1>Looking glass</h1>
		</div>
		<h2 class="mt-4">License</h2>
		
		<p>Copyright &copy; 2022 Célestine GRAMAIZE</p>
		<p>Permission is hereby granted, free of charge, to any person obtaining a copy
		of this software and associated documentation files (the "Software"), to deal
		in the Software without restriction, including without limitation the rights
		to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
		copies of the Software, and to permit persons to whom the Software is
		furnished to do so, subject to the following conditions:</p>
		<p>The above copyright notice and this permission notice shall be included in all
		copies or substantial portions of the Software.</p>
		<p>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
		IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
		FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
		AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
		LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
		OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
		SOFTWARE.
		</p>

		<hr class="my-4" />

		<h2 class="mt-4">Credits</h2>
		<p>This software inspires from <a href="https://phplg.sourceforge.net/" rel="nofollow">PHP Looking Glass v2.0</a>, written by Gabriella Paolini and Dashamir Hoxha.</p>
		
		<hr class="my-4" />

		<h2 class="mt-4">Copying</h2>
		<p>You can grab a copy of this software or fork it from its repository on <a href="https://github.com/dscp46/lglass/">github</a>.</p>
		
        </main>
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
            <div>
	    <span class="text-muted"><?= $app_name ?> ( <a href="./about" class="text-muted">About</a> )</span>
            </div>
        </footer>
    </div>
    <script type="text/javascript" src="./assets/bootstrap.bundle.min.js"></script>
  </body></html>
