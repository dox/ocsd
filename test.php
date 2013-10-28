<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/typeahead.css">
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="js/typeahead.js"></script>
    <script src="js/hogan-2.0.0.js"></script>

<style>
      .container {
        width: 800px;
        margin: 50px auto;
      }

      .typeahead-wrapper {
        display: block;
        margin: 50px 0;
      }

      .tt-dropdown-menu {
        background-color: #fff;
        border: 1px solid #000;
      }

      .tt-suggestion.tt-is-under-cursor {
        background-color: #ccc;
      }

      .triggered-events {
        float: right;
        width: 500px;
        height: 300px;
      }
    </style>
  </head>
  
  <body>
    <div class="container">
      <input class="typeahead" type="text">
    </div>

<script>
$('.typeahead').typeahead({
	name: 'twitter',
	prefetch: './api/studentsAll.php',
	template: '<p class="repo-language">{{name}}</p>',
	engine: Hogan
}).on('typeahead:selected', function($e) {
	var $typeahead = $(this);
	var studentUID = $typeahead[0].value;
	var url = 'index.php?m=students&n=user.php&studentid=' + studentUID;
	
	window.location = url;

});

</script>

  </body>
</html>
