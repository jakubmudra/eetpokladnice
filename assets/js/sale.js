let height=window.innerHeight>0?window.innerHeight:screen.height;$(".sale-main.container").css({width:height+200+"px"}),$(".sale-main.container").height();let elementSelector=".sale-item--inner",lastHeight=0;$(elementSelector).each(function(t){let a=$(this).width(),e=parseInt($(this).css("padding-left").replace("px",""))+parseInt($(this).css("padding-right").replace("px",""));void 0!==$(this).data("span")?$(this).css({height:lastHeight}):(lastHeight=a+e,$(this).css({height:lastHeight}))}),window.onresize=function(t){$(elementSelector).each(function(t){let a=$(this).width(),e=parseInt($(this).css("padding-left").replace("px",""))+parseInt($(this).css("padding-right").replace("px",""));void 0!==$(this).data("span")?$(this).css({height:lastHeight}):(lastHeight=a+e,$(this).css({height:lastHeight}))})};class cart{constructor(t){this.id=t,this.products=[],this.quantities=[],this.getFromLocal()||getFromDB(),this.render()}add(t){if(this.getFromLocal(),this.products.some(a=>a.id===t.id)){let a=this.products.findIndex(a=>a.id===t.id);this.quantities[a]+=1}else this.products.push(t),this.quantities.push(1);this.saveToLocal()}get(){return this.products}getSubTotal(){let t=0;for(var a=0;a<this.products.length;a++)t+=Math.round(this.products[a].price*this.quantities[a]*100)/100;return t.toFixed(2)}render(){let t="";for(var a=0;a<this.products.length;a++)t+="<tr>",t+="<td>"+ +this.quantities[a]+"</td>",t+="<td>"+this.products[a].name+"</td>",t+="<td>"+formatMoney(Math.round(this.products[a].price*this.quantities[a]*100)/100)+"</td>",t+="</tr>";$("#CART").html(t),$("#SUBTOTAL").html(formatMoney(this.getSubTotal())),$("#rounding").html(formatMoney(Math.round(this.getSubTotal())-formatMoney(this.getSubTotal()))),$("#totalLine").html(formatMoney(Math.round(this.getSubTotal())))}saveToLocal(){localStorage.setItem("cart"+this.id,JSON.stringify(this))}removeFromLocal(t){localStorage.removeItem("cart"+t)}getFromLocal(){let t=localStorage.getItem("cart"+this.id);if(null!=t){let a=JSON.parse(t);return this.products=a.products,this.quantities=a.quantities,!0}return!1}}let ct=new cart($("#transactionID").data("tid"));function cancelTransaction(){let t=$("#transactionID").data("tid"),a={trans_id:t};$.ajax({type:"GET",cache:!1,url:"/api/cancelTransaction",data:a}).done(function(){ct.removeFromLocal(t),alert("Done"),window.location.href="/transaction/new"})}function getFromDB(){let t={trans_id:$("#transactionID").data("tid")};$.ajax({type:"GET",cache:!1,url:"/api/getTransactionProducts",data:t}).done(function(t){ct.products=t.products,ct.id=t.id,ct.quantities=t.quantities,ct.render()})}function saveTransaction(t=!0){let a={trans_id:ct.id,products:ct.products,quantity:ct.quantities};ct.products.length>0&&$.ajax({type:"POST",url:"/api/saveTransaction",data:a}).done(function(a){t&&(window.location.href="/transaction/new")})}function openTransactionModal(){saveTransaction(!1),$("#transactionSelectModal").modal("show")}function goToPayment(){let t=$("#transactionID").data("tid");ct.products.length>0?window.location.href="/transaction/pay/"+t:alert("nelze zaplatit prazdnu nakup")}function addCash(t){let a=$("#cashInput>p").text();if("0.00"===a)$("#cashInput>p").html(t);else{checkFormat(a+t)&&$("#cashInput>p").html(a+t)}}function addDelimiter(){let t=$("#cashInput>p").text();t.includes(".")||$("#cashInput>p").html(t+".")}function checkFormat(t){if(t.includes(".")){return!(t.split(".").pop().length>2)}return!0}function removeLast(){$("#cashInput>p").html($("#cashInput>p").text().slice(0,-1))}function plusCash(t){t=parseFloat(t);let a=parseFloat($("#cashInput>p").text());$("#cashInput>p").html((a+t).toFixed(2))}function clearCash(){$("#cashInput>p").html("0.00")}function goBack(){let t=$("#transactionID").data("tid");window.location.href="/transaction/"+t}$(".sale-item--inner.product").click(function(){let t=$(this);ct.add({id:t.data("productid"),name:t.data("name"),price:t.data("price")}),ct.render()}),window.onbeforeunload=function(){saveTransaction(!1),alert("transakce-ulozena")};class payment{constructor(t,a){this.id=t,this.total=a,this.payed=0,this.methods=[],this.allow=!0}doRefund(){this.total=-Math.abs(this.total),this.refund=!0,this.addPaymentMethod("Refundace",this.total),console.log(this.total)}addPaymentMethod(t,a){this.allow&&(this.methods.push({name:t,value:a}),this.payed=this.payed+a,this.payed>=this.total&&(this.toReturn=Math.round(Math.abs(this.getBalance())),this.toReturn>0&&this.methods.push({name:"Vraceno",value:-Math.abs(this.toReturn)}),this.allow=!1,$(".returnValue").html(formatMoney(this.toReturn)),this.refund&&$(".returnValue").html(formatMoney(Math.abs(this.total))),$("#transactionComplete").modal({backdrop:"static",keyboard:!1}),$("#eet_report").hide(),$("#transactionComplete").on("shown.bs.modal",function(t){getEET()})))}getBalance(){return(this.total-this.payed).toFixed(2)}render(){let t="";for(var a=0;a<this.methods.length;a++)t+="<tr>",t+="    <td>"+this.methods[a].name+"</td>",t+="    <td>"+formatMoney(this.methods[a].value)+"</td>",t+="</tr>";$("#return").html(formatMoney(this.getBalance())),$("#paymentMethods").html(t)}}let paymentM=new payment($("#transactionID").data("tid"),Math.round(ct.getSubTotal()));function paidExactly(){paymentM.addPaymentMethod("Přesná částka",paymentM.total-paymentM.payed),clearCash(),paymentM.render()}function paidBy(t){parseFloat($("#cashInput>p").text())>0&&(paymentM.addPaymentMethod(t,parseFloat($("#cashInput>p").text())),clearCash(),paymentM.render())}function formatMoney(t,a=2,e=".",n=","){a=Math.abs(a),a=isNaN(a)?2:a;const i=t<0?"-":"";let o=parseInt(t=Math.abs(Number(t)||0).toFixed(a)).toString(),s=o.length>3?o.length%3:0;return i+(s?o.substr(0,s)+n:"")+o.substr(s).replace(/(\d{3})(?=\d)/g,"$1"+n)+(a?e+Math.abs(t-o).toFixed(a).slice(2):"")}function getEET(){let t={trans_id:$("#transactionID").data("tid"),total:paymentM.total,methods:paymentM.methods};console.log(t),$.ajax({type:"GET",cache:!1,url:"/api/sendTransaction",data:t}).done(function(t){console.log(t),$("#dat_trzby").html(t.dat_trzby.date),$("#bkp").html(t.BKP),"send"==t.state?($("#fik").html(t.FIK),$("#pkp_wrapper").attr("style","display: none!important")):"mustBeResend"==t.state&&($("#pkp").html(t.PKP),$("#fik_wrapper").attr("style","display: none!important")),$("#eet_report").show(),$("#loader_wrapper").hide(),$("#closeEETModal, #closeEETModal1").prop("disabled",!1)})}function discountModal(){$("#discountModal").modal("show")}function authorizeDiscount(){if($discount=$("#discountInput").val(),$discount>100)return void alert("Sleva nemuze byt vice jak 100%!");let t={trans_id:ct.id,discount:$("#discountInput").val()};ct.products.length>0&&$.ajax({type:"POST",url:"/api/authorizeDiscount",data:t}).done(function(t){})}function refundModal(){confirm("Opravdu chcete provest refundaci?")?paymentM.doRefund():alert("Refund zrusen")}function openQuantityModal(){let t=ct.products,a="";for(var e=0;e<t.length;e++)a+="<tr>",a+="    <td>Polozka"+ct.products[e].name+"</td>",a+="    <td>Mnozstvi"+formatMoney(ct.quantities[e].value)+"</td>",a+="</tr>"}