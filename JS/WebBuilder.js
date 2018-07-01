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
    this._PHPdata = JSON.parse(PHPdata);
    // Init the DB editors
    this.DBEditorInit();
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
      if (this.readyState == 4) {
        if (this.status == 200) {
          // The request was successful, return the reply data
          var returnedData = JSON.parse('{"err":""}');
          try {
            var returnedData = JSON.parse(this.responseText);
          } catch(err) {
            console.log(this.responseText);
            returnedData = JSON.parse('{"err":"JSON.parse failed."}');
          }
        } else {
          // The request failed, return error as JSON
          var returnedData = 
            JSON.parse('{"err":"HTTPRequest failed : ' + 
              this.status + '"}');
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
      if (this.readyState == 4) {
        if (this.status == 200) {
          // The request was successful, return the reply data
          var returnedData = JSON.parse('{"err":""}');
          try {
            var returnedData = JSON.parse(this.responseText);
          } catch(err) {
            console.log(this.responseText);
            returnedData = JSON.parse('{"err":"JSON.parse failed."}');
          }
        } else {
          // The request failed, return error as JSON
          var returnedData = 
            JSON.parse('{"err":"HTTPRequest failed : ' + 
              this.status + '"}');
        }
        this._handler(returnedData);
      }
    };
    // Send the request
    xhr.open("POST",url);
    var formData = new FormData(form);
    xhr.send(formData);
  } catch (err) {
    console.log("WebBuilder.HTTPPostRequest " + err.stack);
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

// ------------ DB Editor record manipulation

WebBuilder.prototype.DBEditorReload = function(editor) {
  try {
    // Build the url for reload
    url = "./PHP/WebBuilderDBEdit.php?t=" + editor + "&load=1";
    // Send the request 
    this.HTTPGetRequest(url, function(data) {
      // When we receive the data, update data in memory
      theWB._PHPdata["DBEditors"][data["editor"]] = data["data"];
      // Reset the position in records
      theWB._iRecord[editor] = 0;
      // Refresh the displayed data
      theWB.DBEditorRefresh(data["editor"]);
      // Hide the save icon
      $("#imgWBDBEditorSave" + editor).css("display", "none");
    });
  } catch (err) {
    console.log("WebBuilder.DBEditorReload " + err.stack);
  }
}

WebBuilder.prototype.DBEditorFirst = function(editor) {
  try {
    if (this._iRecord[editor] > 0) {
      // Set the current position in records
      this._iRecord[editor] = 0;
      // Refresh the displayed content
      this.DBEditorRefresh(editor);
    }
  } catch (err) {
    console.log("WebBuilder.DBEditorFirst " + err.stack);
  }
}

WebBuilder.prototype.DBEditorPrevious = function(editor) {
  try {
    if (this._iRecord[editor] > 0) {
      // Set the current position in records
      this._iRecord[editor] -= 1;
      // Refresh the displayed content
      this.DBEditorRefresh(editor);
    }
  } catch (err) {
    console.log("WebBuilder.DBEditorPrevious " + err.stack);
  }
}

WebBuilder.prototype.DBEditorNext = function(editor) {
  try {
    lastRecord = this._PHPdata["DBEditors"][editor].length - 1;
    if (this._iRecord[editor] < lastRecord) {
      // Set the current position in records
      this._iRecord[editor] += 1;
      // Refresh the displayed content
      this.DBEditorRefresh(editor);
    }
  } catch (err) {
    console.log("WebBuilder.DBEditorNext " + err.stack);
  }
}

WebBuilder.prototype.DBEditorLast = function(editor) {
  try {
    lastRecord = this._PHPdata["DBEditors"][editor].length - 1;
    if (this._iRecord[editor] < lastRecord) {
      // Set the current position in records
      this._iRecord[editor] = lastRecord;
      // Refresh the displayed content
      this.DBEditorRefresh(editor);
    }
  } catch (err) {
    console.log("WebBuilder.DBEditorLast " + err.stack);
  }
}

WebBuilder.prototype.DBEditorAdd = function(editor) {
  try {
    // Set the current position in records
    this._iRecord[editor] = 
      this._PHPdata["DBEditors"][editor].length;
    // Create a new record
    this._PHPdata["DBEditors"][editor][this._iRecord[editor]] = {};
    // Refresh the displayed content
    this.DBEditorRefresh(editor);
  } catch (err) {
    console.log("WebBuilder.DBEditorAdd " + err.stack);
  }
}

WebBuilder.prototype.DBEditorDelete = function(editor) {
  try {
    // If the current record is not already requested for deletion
    var record = 
      this._PHPdata["DBEditors"][editor][this._iRecord[editor]];
    if (record["DBEditorDeleted"] === undefined ||
      record["DBEditorDeleted"] === false) {
      // Mark this record as requested for deletion
      record["DBEditorDeleted"] = true;
      // Turn on the delete button
      $("#imgWBDBEditorDelete" + editor).attr("src",
        "./Img/Icons/recordIconMinusOn.png");
      // Display the save icon
      $("#imgWBDBEditorSave" + editor).css("display", "inline-block");
    } else {
      // Cancel the request for deletion
      record["DBEditorDeleted"] = false;
      // Turn off the delete button
      $("#imgWBDBEditorDelete" + editor).attr("src",
        "./Img/Icons/recordIconMinusOff.png");
    }
  } catch (err) {
    console.log("WebBuilder.DBEditorDelete " + err.stack);
  }
}

WebBuilder.prototype.DBEditorSave = function(editor) {
  try {
    // For each record
    for (var r in this._PHPdata["DBEditors"][editor]) {
      var record = this._PHPdata["DBEditors"][editor][r];
      // If this record is requested for deletion
      if (record["DBEditorDeleted"] !== undefined &&
        record["DBEditorDeleted"] == true) {
        // Build the url for deletion
        url = "./PHP/WebBuilderDBEdit.php?t=" + editor + "&delete=1";
        // Create a form element
        var form = document.createElement("form");
        // Add the Reference field and its value to the form
        var inp = document.createElement("input");
        inp.setAttribute("type", "text");
        inp.setAttribute("name", "Reference");
        inp.setAttribute("value", record["Reference"]);
        form.appendChild(inp);
        // Send the request 
        this.HTTPPostRequest(url, form, function(data) {
// TODO : should manage error
        });
        //record["DBEditorDeleted"] = false;
        delete this._PHPdata["DBEditors"][editor][r];
      // Else, if this record was modified
      } else if (record["DBEditorModified"] === true) {
        // If it's a new record
        if (record["Reference"] === undefined ||
          record["Reference"] == "") {
          // Build the url for addition
          url = "./PHP/WebBuilderDBEdit.php?t=" + editor + "&add=1";
          // Create a form element
          var form = document.createElement("form");
          // For each field
          for (var field in record) {
            // If it's not an internal field
            if (field != "DBEditorDeleted" &&
              field != "DBEditorModified") {
              // Add the field and its encoded value to the form
              var inp = document.createElement("input");
              inp.setAttribute("type", "text");
              inp.setAttribute("name", field);
              inp.setAttribute("value", record[field]);
              form.appendChild(inp);
            }
          }
          // Send the request 
          this.HTTPPostRequest(url, form, function(data) {
// TODO : should manage error
          });
        // Else, it's an existing record which has been modified
        } else {
          // Build the url for modification
          url = "./PHP/WebBuilderDBEdit.php?t=" + editor + "&modif=1";
          // Create a form element
          var form = document.createElement("form");
          // For each field
          for (var field in record) {
            // If it's not an internal field
            if (field != "DBEditorDeleted" &&
              field != "DBEditorModified") {
              // Add the field and its encoded value to the form
              var inp = document.createElement("input");
              inp.setAttribute("type", "text");
              inp.setAttribute("name", field);
              inp.setAttribute("value", record[field]);
              form.appendChild(inp);
            }
          }
          // Send the request 
          this.HTTPPostRequest(url, form, function(data) {
// TODO : should manage error
          });
        }
        record["DBEditorModified"] = false;
      }
    }
    // Hide the save icon
    $("#imgWBDBEditorSave" + editor).css("display", "none");
    // Refresh the displayed data
    this.DBEditorRefresh(editor);
  } catch (err) {
    console.log("WebBuilder.DBEditorSave " + err.stack);
  }
}

// ------------ Initialise the DB Editor

WebBuilder.prototype.DBEditorInit = function() {
  try {
    // Indices of current record in editors
    this._iRecord = new Array();
    // For each editors
    for (var editor in this._PHPdata["DBEditors"]) {
      // Set the current position in records
      this._iRecord[editor] = 0;
      // Refresh the displayed content
      this.DBEditorRefresh(editor);
      // Attach the onchange event of fields
      $("#divWBDBEditorFieldData" + editor + " input").bind("input", 
        {'editor':editor},
        function(event) {
          var field = this.id.substr(
            ("inpWBDBEditorField" + event.data.editor).length);
          theWB.DBEditorNotifyChange(event.data.editor, field);
        });
      $("#divWBDBEditorFieldData" + editor + " select").bind("input", 
        {'editor':editor},
        function(event) {
          var field = this.id.substr(
            ("inpWBDBEditorField" + event.data.editor).length);
          theWB.DBEditorNotifyChange(event.data.editor, field);
        });
    }
  } catch (err) {
    console.log("WebBuilder.DBEditorInit " + err.stack);
  }
}

// ------------ Refresh the content of the fields of the current record

WebBuilder.prototype.DBEditorRefresh = function(editor) {
  try {
    // Set the last position index
    var nbRecord = this._PHPdata["DBEditors"][editor].length;
    $("#spanDBEditorLastPos" + editor).html(nbRecord);
    // Update the current position
    var displayPos = this._iRecord[editor] + 1;
    var nbRecord = this._PHPdata["DBEditors"][editor].length;
    $("#spanDBEditorCurPos" + editor).html(displayPos);
    // Current record
    var record = 
      this._PHPdata["DBEditors"][editor][this._iRecord[editor]];
    // Empty all the fields
    $("#divWBDBEditorFieldData" + editor + " input").val("");
    $("#divWBDBEditorFieldData" + editor + " select").val("");
    // If the record exists
    if (record !== undefined) {
      $("#divWBDBEditorData" + editor).css("display", "block");
      $("#divWBDBEditorDataDeleted" + editor).css("display", "none");
      // If the record exists for the current position
      if (Object.keys(record).length > 0) {
        // For each fields
        for (field in record) {
          // Id of the field in the DOM
          var idField = "inpWBDBEditorField" + editor + field;
          // If this field is a select input and it's related to 
          // another displayed DB editor, the options may have changed,
          // then we update them too
          // WARNING: if there is a condition on the DB editor of the 
          // related table, this condition will affect displayed
          // options here too ! 
          if ($("#" + idField).is("select")) {
            fieldDef = this._PHPdata["DBEditorModels"][editor][field];
            args = fieldDef.split(",");
            if (this._PHPdata["DBEditors"][args[1]] !== undefined &&
              this._PHPdata["DBEditors"][args[1]][0][args[2]] !== 
                undefined &&
              this._PHPdata["DBEditors"][args[1]][0][args[3]] !== 
                undefined) {
              // Drop the previous options
              $("#" + idField + " option").remove();
              // Recreate the options
              for (var opt in this._PHPdata["DBEditors"][args[1]]) {
                $("#" + idField).append($('<option>', { 
                  value: 
                    this._PHPdata["DBEditors"][args[1]][opt][args[2]],
                  text : 
                    this._PHPdata["DBEditors"][args[1]][opt][args[3]]
                }));
              }
            }
          }
          // Val of the field
          var valField = record[field];
          // Update the field content
          $("#" + idField).val(valField);
        }
      }
      // If this record is not requested for deletion
      if (record["DBEditorDeleted"] === undefined ||
        record["DBEditorDeleted"] === false) {
        // Turn off the delete button
        $("#imgWBDBEditorDelete" + editor).attr("src",
          "./Img/Icons/recordIconMinusOff.png");
      } else {
        // Turn on the delete button
        $("#imgWBDBEditorDelete" + editor).attr("src",
          "./Img/Icons/recordIconMinusOn.png");
      }
    // Else, it means it has been deleted
    } else {
      $("#divWBDBEditorData" + editor).css("display", "none");
      $("#divWBDBEditorDataDeleted" + editor).css("display", "block");
    }
  } catch (err) {
    console.log("WebBuilder.DBEditorRefresh " + err.stack);
  }
}


// ------------ Manage modification by the user of data

WebBuilder.prototype.DBEditorNotifyChange = function(editor, field) {
  try {
    // Display the save icon
    $("#imgWBDBEditorSave" + editor).css("display", "inline-block");
    // Get the current record
    var record = 
      this._PHPdata["DBEditors"][editor][this._iRecord[editor]];
    // If the record doesn't exist for the current position
    if (record === undefined) {
      this._PHPdata["DBEditors"][editor][this._iRecord[editor]] = 
        new Array();
      record = 
      this._PHPdata["DBEditors"][editor][this._iRecord[editor]];
    }
    // Set the flag in data to remember which one has been modified
    record["DBEditorModified"] = true;
    // Update the data in memory
    var idField = "inpWBDBEditorField" + editor + field;
    record[field] = $("#" + idField).val();
  } catch (err) {
    console.log("WebBuilder.DBEditorNotifyChange " + err.stack);
  }
}

