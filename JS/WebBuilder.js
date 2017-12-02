/* ============= WebBuilder.js =========== */

// ------------ Global variables
var theWB = {};

// ------------ WebBuilder class

function WebBuilder(PHPdata) {
  try {
    // Image preloader
    this._imgPreload = new Array();
    // Hook for the HTTP request returned value
    this._hookHTTPRequest = WBDefaultHandlerHTTPRequest;
    // Data received from PHP
    this._PHPdata = PHPdata;
  } catch (err) {
    console.log("WebBuilder " + err.stack);
  }
}

// ------------ HTTP Get request

WebBuilder.prototype.HTTPGetRequest = function(url, handler) {
  try {
    // Create the request object
    if (window.XMLHttpRequest) {
      var xhr = new XMLHttpRequest();
    } else {
      var xhr = new ActiveXObject('Microsoft.XMLHTTP');
    }
    // Hook for the reply
    xhr._handler = handler;
    xhr.onreadystatechange = function() {
      // If the request is ready
      if (xhr.readyState == 4) {
        if (xhr.status == 200) {
          // The request was successful, return the reply data
          var returnedData = JSON.parse(xhr.responseText);
        } else {
          // The request failed, return error as JSON
          var returnedData = 
            JSON.parse('{"err":"HTTPRequest failed : ' + 
              xhr.status + '"}');
        }
        this._handler(returnedData);
      }
    };
    // Send the request
    xhr.open('GET', url);
    xhr.send();
  } catch (err) {
    console.log("WebBuilder.HTTPGetRequest " + err.stack);
  }
}

// ------------ HTTP Post request

WebBuilder.prototype.HTTPPostRequest = function(url, form, handler) {
  try {
    // Create the request object
    if (window.XMLHttpRequest) {
      var xhr = new XMLHttpRequest();
    } else {
      var xhr = new ActiveXObject('Microsoft.XMLHTTP');
    }
    // Hook for the reply
    xhr._handler = handler;
    xhr.onreadystatechange = function() {
      // If the request is ready
      if (xhr.readyState == 4) {
        if (xhr.status == 200) {
          // The request was successful, return the reply data
          var returnedData = JSON.parse(xhr.responseText);
        } else {
          // The request failed, return error as JSON
          var returnedData = 
            JSON.parse('{"err":"HTTPRequest failed : ' + 
              xhr.status + '"}');
        }
        this._handler(returnedData);
      }
    };
    // Send the request
    xhr.open("POST",url);
    var formData = new FormData(form);
    xhr.send(formData);
  } catch (err) {
    console.log("WebBuilder.HTTPGetRequest " + err.stack);
  }
}

// ------------ Function to set the handler for HTTP request

WebBuilder.prototype.SetHandlerHTTPRequest = function(handler) {
  try {
    theWB._handlerHTTPRequest = handler;
  } catch (err) {
    console.log("WebBuilder.SetHandlerHTTPRequest " + err.stack);
  }
}

// ------------ Default handler to process HTTP request reply

function WBDefaultHandlerHTTPRequest(data) {
  try {
    console.log("The handler for HTTP request is not defined.");
    console.log("Use WebBuilder.SetHandlerHTTPRequest(handlerFunction)" +
      " to set the handler before sending request. The handler has" +
      " one argument: the returned JSON value parsed into an object.");
  } catch (err) {
    console.log("WBDefaultHandlerHTTPRequest " + err.stack);
  }
}

// ------------ Body.OnLoad handler

function WBBodyOnLoad(PHPdata) {
  try {
    // Create the WebBuilder
    theWB = new WebBuilder(PHPdata);
  } catch (err) {
    console.log("WBBodyOnLoad " + err.stack);
  }
}

// ------------ Image loader
// images: array of url of loaded images, url relative to ./Img/
// ids: respective array of id of <img> elements to which the loaded 
// image should be affected, if null nothing happen (but the image 
// is still available in cache, ready to be displayed later)
// waiting: image that should be displayed while the real image is 
// loading (if not null and respective id is defined)
WebBuilder.prototype.LoadImg = function(images, ids, waiting) {
  try {
    if (Array.isArray(images) == true && 
      Array.isArray(ids) == true &&
      images.length == ids.length) {
      // Preload images for fast rendering
      for (var iImg = 0; iImg < images.length; iImg++) {
        var img = new Image();
        if (document.getElementById(ids[iImg]) !== null) {
          img._id = ids[iImg];
          img.onload = function () {
            document.getElementById(this._id).src = this.src;
            console.log("WebBuilder: " + this.src + " loaded");
          }
          if (waiting !== '') {
            document.getElementById(ids[iImg]).src = 
              "./Img/" + waiting;
          }
        }
        img.src = "./Img/" + images[iImg];
        this._imgPreload.push(img);
      }
    } else {
      throw("Argument is not an array");
    }
  } catch (err) {
    console.log("WebBuilder.PreloadImg " + err.stack);
  }
}

// ------------ Image uploader
// Handler for the button to hide the ugly default one
WebBuilder.prototype.UploadImgBtnClick = function(id) {
  try {
    document.getElementById('inpImgUpload' + id).click();
  } catch (err) {
    console.log("WebBuilder.UploadImgBtnClick " + err.stack);
  }
}
// Handler triggered when the user choose a file
// Request the upload with the user defined available format, max size
// and callback function to process the result of the upload
// Example of object sent to the callback function:
// {"err":0,"sizeMax":"500000","formats":"jpg,JPG","size":58929,
// "targetFile":"..\/Img\/UploadImg\/000.jpg","fileType":"jpg",
// "msg":""}
WebBuilder.prototype.UploadImgInpChange = function(id, formats,
  sizeMax, callback) {
  try {
    var val = document.getElementById('inpImgUpload' + id).value;
    if (val != "") {
      url = './PHP/WebBuilderUploadImg.php?s=' + sizeMax + 
        '&f=' + formats;
      var file = document.getElementById('inpImgUpload' + id).files[0];
      if (file === undefined) { 
        callback({'err':'1'});
      } else {
        if (window.XMLHttpRequest) {
          xhr = new XMLHttpRequest();
        } else {
          xhr = new ActiveXObject('Microsoft.XMLHTTP');
        }
        xhr.onreadystatechange = 
          function() {
            if (xhr.readyState == 4) {
              if (xhr.status == 200) {
                data = xhr.responseText;
              } else {
                data = {'err':'2'};
              }
              callback(JSON.parse(data));
            }
          }
        xhr.open("POST",url);
        var formData = new FormData(
          document.getElementById('formImgUpload' + id));
        xhr.send(formData);
      }
    }
  } catch (err) {
    console.log("WebBuilder.UploadImgInpChange " + err.stack);
  }
}

