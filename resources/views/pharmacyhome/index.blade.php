<!DOCTYPE html>
@extends('pharmacylayout')
<html>
<head>
  <script src="js/jquery-1.11.3.min.js"></script>
  <title>Pharmacy Home</title>
</head>
<body>
  @section('index') 
    <div id="prescriptioninputdiv">
      <button class="button" data-modal="modalOne" style="position: fixed;">Make a Purchase Slip</button>
        <div id="modalOne" class="modal" style="display: block;">
            <div class="modal-content" id="modalContent"> 
              <a class="close" id="closebtn">&times;</a> 
              <div class="contact-form" id="contactForm">
                  <form action="{{url('purchase')}}" method="post">
                    <button type="button" id="reloadbtn" class="btn-green" onclick = "updateDiv()">
                      <img class="icon" src="{{ asset('images/reload-6x-white.png') }}"> Reload
                    </button><br>
                  {{csrf_field()}}
                    </font>
                    <table id="myTable">
                      <thead>
                        <tr style="background-color: blue; color: white;">
                          <td align="center" style="width: 145px">Product</td>
                          <td align="center" style="width: 55px">MRP/Unit</td>
                          <td align="center" style="width: 55px">QTY(Unit)</td>
                          <td align="center" style="width: 55px">Total</td>
                        </tr>
                      </thead>
                      <tbody id="myTableBody"></tbody>
                      <tr><td><input type="button" name="" value="Add More" onclick="addrow()" 
                               style="width:145px; background-color: blue; color: white;"></td></tr>
                      <tr>
                          <td colspan="2" align="right">Total</td>
                          <td colspan="2">
                            <input type="text" readonly="readonly" style="border: none; width: 75px;" name="total" id="total">
                          </td>
                      </tr>
                      <tr>
                          <td colspan="2" align="right">Discount</td>
                          <td colspan="2">
                            <input type="number" min="0" style="border: none; width: 75px;" name="discount" id="discount">
                          </td>
                      </tr>
                      <tr>
                          <td colspan="2" align="right">Net Amount</td>
                          <td colspan="2">
                            <input type="text" readonly="readonly" style="border: none; width: 75px;" name="netammount" id="netammount">
                          </td>
                      </tr>
                    </table>            
                    <br>
                    <label id="label" style="color:red"><font color="red">{{session('msg')}}</font></label>
                    <br>
                    <button type="button"  onclick="calculate()"><font size="5">Calculate</font></button>
                    <br><br><br><br><br>
                    Customer Contactno. <input type="text" id="admininput" name="cuscontactno" class="cuscontact" 
                                               style="width: 150px" maxlength="11">
                    <font color="red">
                      @if ($errors->has('tokennumber'))
                        {{ $errors->first('tokennumber') }}
                      @endif
                    </font>
                    <br><br>
                    <label id="label" style="color:red"><font color="red">{{session('printmsg')}}</font></label>
                    <button type="submit" name="submit_change"><font size="5">Print</font></button>
                </form>
              </div>
            </div>
          </div>
          Search Prescription
        <input type="text" name="contactno" style="width: 250px" id="search" maxlength="11" placeholder="Search prescription by patient contact.no"><input type="button" id="searchpresbutton" value="Search" onclick="searchPrescription()">
        <table style="background-color:white;min-width:300px;margin:3px auto;">
          <tbody>
              <tbody class="labels" id="success">
              </tbody>  
          </tbody>
        </table>
    </div>
    <div id="purchasecheckunitdiv">
      <input type="text" style="width: 250px;" id="searchunit" placeholder="Search product's unit in stock">
      <input type="text" id="unitleft" style="width: 180px; border-style:none; background-color: #f2f2f2" readonly="readonly">
    </div>
    <script>
      function searchPrescription()
      {    
        var search = $('#search').val();
        console.log(search);
          $.ajax({
            type:"get",
                url:'{{URL::to("prescription/search")}}',
                {{--url:'{{route('prescription.search')}}',--}}
            data:{
              search:search,
              _token:$('#signup-token').val()
              },
            datatype:'html',
            
            success:function(response){
              console.log(response);
              $("#success").html(response);
            }
          });
      }

      $("#searchunit").autocomplete({
        source : '{!!URL::route('pharmacyinventoryunit.search')!!}',
        minLenght:1,
        autoFocus:true,
        select:function(e, ui){
          $('#unitleft').val(ui.item.unit);
        }
      });

      var flag = 0;
      
      var modalBtns = [...document.querySelectorAll(".button")];
      modalBtns.forEach(function(btn){
        btn.onclick = function() {
          var modal = btn.getAttribute('data-modal');         
          document.getElementById(modal).style.display = "block";
        }
      });

      var closeBtns = [...document.querySelectorAll(".close")];
      closeBtns.forEach(function(btn){
        btn.onclick = function() {
          var modal = btn.closest('.modal');
          modal.style.display = "none";
        }
      });

      window.onclick = function(event) {
        if (event.target.className === "modal") {
          event.target.style.display = "none";
        }
      }
      
  //Add More Row
  var i=0;
  function addrow(){
    var table = document.getElementById("myTable");
    var x = document.getElementById("myTable").rows.length;
    var z = document.getElementById("myTableBody").rows.length/2;
    i = x - z - 5;

    var row = table.insertRow(x-4);
    row.setAttribute("id", "r"+i);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);

    let newinputbox1 = document.createElement("input");
    newinputbox1.setAttribute("type", "text");
    newinputbox1.setAttribute("name", "medicine[]");
    newinputbox1.setAttribute("id", "cmn"+i);
    newinputbox1.setAttribute("class", "test");
    newinputbox1.style.width = "125px";

    let newinputboxhidden = document.createElement("input");
    newinputboxhidden.setAttribute("type", "text");
    newinputboxhidden.setAttribute("name", "medicineid[]");
    newinputboxhidden.setAttribute("id", "cmn"+i+"id");
    newinputboxhidden.style.display = "none";

    let newinputboxhidden2 = document.createElement("input");
    newinputboxhidden2.setAttribute("type", "text");
    newinputboxhidden2.setAttribute("name", "unit[]");
    newinputboxhidden2.setAttribute("id", "cmn"+i+"unit");
    newinputboxhidden2.style.display = "none";

    let newinputbox2 = document.createElement("input");
    newinputbox2.setAttribute("type", "number");
    newinputbox2.setAttribute("name", "pt[]");
    newinputbox2.setAttribute("min", "0");
    newinputbox2.style.width = "55px";
    newinputbox2.style.border = "none";
    newinputbox2.style.backgroundColor = " #f2f2f2";
    newinputbox2.setAttribute("id", "cmn"+i+"p");
    newinputbox2.setAttribute("readonly", "readonly");

    var selector = 'input.test';
    $(document).on('keydown.autocomplete', selector, function() {
      $(this).autocomplete({
        source : '{!!URL::route('pharmacypurchase.search')!!}',
        minLenght:1,
        autoFocus:true,
        select:function(e, ui){
        var y = $(this).attr('id');
        $('#' +y +"id").val(ui.item.id);
        $('#' +y +"p").val(ui.item.mrp);
        $('#' +y +"unit").val(ui.item.unit);
        }
      })
    });


    let newinputbox3 = document.createElement("input");
    newinputbox3.setAttribute("type", "number");
    newinputbox3.setAttribute("name", "q[]");
    newinputbox3.setAttribute("min", "0");
    newinputbox3.style.width = "55px";
    newinputbox3.setAttribute("id", "cq"+i);

    let newinputbox4 = document.createElement("input");
    newinputbox4.setAttribute("type", "number");
    newinputbox4.setAttribute("name", "t[]");
    newinputbox4.setAttribute("min", "0");
    newinputbox4.style.width = "55px";
    newinputbox4.style.border = "none";
    newinputbox4.style.backgroundColor = " #f2f2f2";
    newinputbox4.setAttribute("id", "ct"+i);
    newinputbox4.setAttribute("readonly", "readonly");

    let newinputbox5 = document.createElement("input");
    newinputbox5.setAttribute("type", "button");
    newinputbox5.setAttribute("value", "Remove");
    newinputbox5.setAttribute("onclick", "remove(this.id)");
    newinputbox5.style.width = "65px";
    newinputbox5.style.backgroundColor = "red";
    newinputbox5.style.color = "white";
    newinputbox5.setAttribute("id", i);

    var input1 = cell1.appendChild(newinputbox1);
    var input2 = cell2.appendChild(newinputbox2);
    var input3 = cell3.appendChild(newinputbox3);
    var input4 = cell4.appendChild(newinputbox4);
    var input5 = cell5.appendChild(newinputbox5);
    var input6 = cell1.appendChild(newinputboxhidden);
    var input7 = cell1.appendChild(newinputboxhidden2);
  }
    ///Remove Item
  function remove(s) {
    document.getElementById("cmn"+s).value="";
    document.getElementById("cmn"+s+"id").value="";
    document.getElementById("cmn"+s+"p").value="";
    document.getElementById("cq"+s).value="";
    document.getElementById("ct"+s).value="";
  }


  //Calculation
  function calculate() {
    var total = 0.00;
    var rowtotal= 0.00;
    var rowq = 0;
    var rowprice = 0.00;
    var netammount = 0.00;
    var discount = 0.00;
    var flag2 = 0;
    var x;
    var table = document.getElementById("myTable");
    var y = document.getElementById("myTable").rows.length;  
    var z = document.getElementById("myTableBody").rows.length/2;
    x = y - z - 5;
    if(document.getElementById("discount").value == "")
        {
            document.getElementById("discount").value = "0.00";
        }
    discount = parseFloat(document.getElementById("discount").value);
      
    for(var i=0; i<x; i++)  
    {
      document.getElementById("label").innerHTML = "";
      if (document.getElementById("cmn"+i+"p").value == "" && document.getElementById("cq"+i).value == "") 
      {
        document.getElementById("cmn"+i+"p").value = "0.00";
        document.getElementById("cq"+i).value = "0";
      }
      if (document.getElementById("cq"+i).value == "") 
      {
        document.getElementById("cq"+i).value = "0";
      }
      rowprice = parseFloat(document.getElementById("cmn"+i+"p").value);
      rowq = parseInt(document.getElementById("cq"+i).value);
      rowtotal = parseFloat(rowprice * rowq);
      document.getElementById("ct"+i).value = parseFloat(rowtotal);
      total = total + parseFloat(document.getElementById("ct"+i).value);
      
      if(Number.isInteger(total))
        {
          document.getElementById("total").value = total+".00Tk";
        }
      else
        {
          document.getElementById("total").value = parseFloat(total).toFixed(2)+"Tk";   
        }
   } 
     if(flag2==0)
     {
     if(total<discount)
        {
            document.getElementById("discount").value = "0.00";
        }
     discount = parseFloat(document.getElementById("discount").value);
     netammount = total - discount;
     if(Number.isInteger(netammount))
        {
          document.getElementById("netammount").value = netammount+".00Tk";
        }
     else
        {
          document.getElementById("netammount").value = parseFloat(netammount).toFixed(2)+"Tk";   
        }
     }
  }

  function updateDiv()
  { 
    $( "#contactForm" ).load(window.location.href + " #contactForm" );
  }

  function makeBill(preId, c){
    document.getElementById('admininput').value = "0"+c;
    var search = preId;
        console.log(search);
          $.ajax({
            type:"get",
                url:'{{URL::to("pharmacypurchase/makebill")}}',
                {{--url:'{{route('pharmacypurchase.makebill')}}',--}}
            data:{
              search:search,
              _token:$('#signup-token').val()
              },
            datatype:'html',
            
            success:function(response){
              console.log(response);
              $("#myTableBody").html(response);
            }
          });
  }

    </script>
  @endsection 
</body>
</html>



