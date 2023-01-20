<?php

    $currentDirectory = getcwd();
    $uploadDirectory = "/upload/";
    $errors = []; // Store errors here
    $return = "";

    $fileExtensionsAllowed = ['pdf']; // These will be the only file extensions allowed 

    $fileName = $_FILES['pdf']['name'];
    $fileSize = $_FILES['pdf']['size'];
    $fileTmpName  = $_FILES['pdf']['tmp_name'];
    $fileType = $_FILES['pdf']['type'];
    $fileExtension = strtolower(end(explode('.',$fileName)));
    
    $nome =  substr(md5(rand().$fileName.rand()), 0, 6).'.'.$fileExtension;

    $uploadPath = $currentDirectory . $uploadDirectory . basename($nome); 
    if (isset($_FILES['pdf'])) {
      if (! in_array($fileExtension,$fileExtensionsAllowed)) {
        $errors[] = "Extensão não permitida. Por favor, envie um arquivo pdf.";
      }

      if ($fileSize > 20000000) {
        $errors[] = "O arquivo excede o limite de 20MB";
      }
     
      if (empty($errors)) {
        $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
       
        if ($didUpload) {
          $return = "O arquivo " . basename($fileName) . " Foi enviado";
        } else {
          $return = "Houve um erro no envio do arquivo.$uploadPath";
        }
      } else {
        foreach ($errors as $error) {
          $return += $error . "Erros" . "\n";
        }
      }

    }
    ?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/ico.png" />
    <title>Assinador de PDF</title>
    <style>
      @import url("https://fonts.googleapis.com/css?family=Lato");
      * {
        margin: 0;
        padding: 0;
        font-family: Lato, Arial;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
      }
      body {
        color: #fff;
        padding: 55px 25px;
        background-color: #888;
      }
      h1 {
        font-weight: normal;
        font-size: 40px;
        font-weight: normal;
        text-transform: uppercase;
      }
      h1 span {
        font-size: 13px;
        display: block;
        padding-left: 4px;
      }
      p {
        margin-top: 100px;
      }
      p a {
        text-transform: uppercase;
        text-decoration: none;
        display: inline-block;
        color: #111;
        padding: 5px 10px;
        margin: 0 5px;
        background-color: #fafafa;
        -moz-transition: all 0.2s ease-in;
        -o-transition: all 0.2s ease-in;
        -webkit-transition: all 0.2s ease-in;
        transition: all 0.2s ease-in;
      }
      p a:hover {
        background-color: #f2f2f2;
      }
      .custom-file-upload-hidden {
        display: none;
        visibility: hidden;
        position: absolute;
        left: -9999px;
      }
      .custom-file-upload {
        display: block;
        width: auto;
        font-size: 16px;
        margin-top: 30px;
      }
      .custom-file-upload label {
        display: block;
        margin-bottom: 5px;
      }
      .file-upload-wrapper {
        position: relative;
        margin-bottom: 5px;
      }
      .file-upload-input {
        width: 300px;
        color: #111;
        font-size: 16px;
        padding: 11px 17px;
        border: none;
        background-color: #fff;
        -moz-transition: all 0.2s ease-in;
        -o-transition: all 0.2s ease-in;
        -webkit-transition: all 0.2s ease-in;
        transition: all 0.2s ease-in;
        float: left;
        /* IE 9 Fix */
      }
      .file-upload-input:hover,
      .file-upload-input:focus {
        background-color: #f2f2f2;
        outline: none;
      }
      .file-upload-button {
        cursor: pointer;
        display: inline-block;
        color: #111;
        font-size: 16px;
        text-transform: uppercase;
        padding: 11px 20px;
        border: none;
        margin-left: -1px;
        background-color: #e6e6e6;
        float: left;
        /* IE 9 Fix */
        -moz-transition: all 0.2s ease-in;
        -o-transition: all 0.2s ease-in;
        -webkit-transition: all 0.2s ease-in;
        transition: all 0.2s ease-in;
      }
      .file-upload-button:hover {
        background-color: #cccccc;
      }
      #alerta {
        color:red;
        margin-top:30px
      }
    </style>
  </head>

  <body>
    <h1>Assinador de PDF <span>Para certificados eletrônicos</span></h1>

    <p id="alerta"><?=$return?></p>
    <form
      method="POST"
      enctype="multipart/form-data"
    >
      <div class="custom-file-upload">
        <label for="file">Arquivo PDF: </label>
        <input
          type="file"
          id="pdf"
          name="pdf"
          accept="application/pdf"
          required
        />
      </div>
      <br />
      <br />
      <!--div class="custom-file-upload">
        <label for="file">Assinatura eletrônica: </label>
        <input type="file" id="assinatura" name="assinatura" />
      </div-->

      <!--p><a href="#">Enviar</a></p-->
      <input type="submit" value="Enviar" />
    </form>
    <script src="assets/tools/jquery-3.5.1.min.js"></script>
    <script id="rendered-js">
      //Reference:
      //https://www.onextrapixel.com/2012/12/10/how-to-create-a-custom-file-input-with-jquery-css3-and-php/
      (function ($) {
        // Browser supports HTML5 multiple file?
        var multipleSupport = typeof $("<input/>")[0].multiple !== "undefined",
          isIE = /msie/i.test(navigator.userAgent);

        $.fn.customFile = function () {
          return this.each(function () {
            var $file = $(this).addClass("custom-file-upload-hidden"), // the original file input
              $wrap = $('<div class="file-upload-wrapper">'),
              $input = $('<input type="text" class="file-upload-input" />'),
              // Button that will be used in non-IE browsers
              $button = $(
                '<button type="button" class="file-upload-button">Selecionar Arquivo</button>'
              ),
              // Hack for IE
              $label = $(
                '<label class="file-upload-button" for="' +
                  $file[0].id +
                  '">Select a File</label>'
              );

            // Hide by shifting to the left so we
            // can still trigger events
            $file.css({
              position: "absolute",
              left: "-9999px",
            });

            $wrap
              .insertAfter($file)
              .append($file, $input, isIE ? $label : $button);

            // Prevent focus
            $file.attr("tabIndex", -1);
            $button.attr("tabIndex", -1);

            $button.click(function () {
              $file.focus().click(); // Open dialog
            });

            $file.change(function () {
              var files = [],
                fileArr,
                filename;

              // If multiple is supported then extract
              // all filenames from the file array
              if (multipleSupport) {
                fileArr = $file[0].files;
                for (var i = 0, len = fileArr.length; i < len; i++) {
                  files.push(fileArr[i].name);
                }
                filename = files.join(", ");

                // If not supported then just take the value
                // and remove the path to just show the filename
              } else {
                filename = $file.val().split("\\").pop();
              }

              $input
                .val(filename) // Set the value
                .attr("title", filename) // Show filename in title tootlip
                .focus(); // Regain focus
            });

            $input.on({
              blur: function () {
                $file.trigger("blur");
              },
              keydown: function (e) {
                if (e.which === 13) {
                  // Enter
                  if (!isIE) {
                    $file.trigger("click");
                  }
                } else if (e.which === 8 || e.which === 46) {
                  // Backspace & Del
                  // On some browsers the value is read-only
                  // with this trick we remove the old input and add
                  // a clean clone with all the original events attached
                  $file.replaceWith(($file = $file.clone(true)));
                  $file.trigger("change");
                  $input.val("");
                } else if (e.which === 9) {
                  // TAB
                  return;
                } else {
                  // All other keys
                  return false;
                }
              },
            });
          });
        };

        // Old browser fallback
        if (!multipleSupport) {
          $(document).on("change", "input.customfile", function () {
            var $this = $(this),
              // Create a unique ID so we
              // can attach the label to the input
              uniqId = "customfile_" + new Date().getTime(),
              $wrap = $this.parent(),
              // Filter empty input
              $inputs = $wrap
                .siblings()
                .find(".file-upload-input")
                .filter(function () {
                  return !this.value;
                }),
              $file = $(
                '<input type="file" id="' +
                  uniqId +
                  '" name="' +
                  $this.attr("name") +
                  '"/>'
              );

            // 1ms timeout so it runs after all other events
            // that modify the value have triggered
            setTimeout(function () {
              // Add a new input
              if ($this.val()) {
                // Check for empty fields to prevent
                // creating new inputs when changing files
                if (!$inputs.length) {
                  $wrap.after($file);
                  $file.customFile();
                }
                // Remove and reorganize inputs
              } else {
                $inputs.parent().remove();
                // Move the input so it's always last on the list
                $wrap.appendTo($wrap.parent());
                $wrap.find("input").focus();
              }
            }, 1);
          });
        }
      })(jQuery);

      $("input[type=file]").customFile();
    </script>
  </body>
</html>