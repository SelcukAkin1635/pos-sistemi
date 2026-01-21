<?php
session_name("kasiyer_session");
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kasiyer') {
    header("Location: index.php?error=Yetkisiz erişim!");
    exit;
}

include "db.php";

$masalar = $conn->query("
    SELECT m.*, a.onayli 
    FROM masalar m
    LEFT JOIN adisyonlar a ON a.masa_id=m.id AND a.kapali=0
");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Kasiyer Paneli</title>
  <style>
    body { font-family: Arial, sans-serif; margin:0; background:#f4f6f9; }
    header { display:flex; justify-content:space-between; align-items:center; background:#4e73df; color:#fff; padding:10px 20px; }
    header a { color:#fff; text-decoration:none; font-weight:bold; }
    .container { display:flex; height:calc(100vh - 50px); }

    /* Masalar */
    .masalar-panel { width:25%; border-right:2px solid #ddd; padding:20px; overflow-y:auto; }
    .masa-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(100px,1fr)); gap:10px; }
    .masa { padding:20px; border-radius:10px; color:#fff; font-weight:bold; cursor:pointer; text-align:center; transition:transform 0.2s; }
    .masa:hover { transform:scale(1.05); }
    .masa.bos { background:#1cc88a; }
    .masa.dolu { background:#e74a3b; }
    .masa.bekliyor { background:#f6c23e; color:#000; } /* sarı */
    /* Sipariş Özeti */
    .siparis-panel { width:40%; padding:20px; border-right:2px solid #ddd; overflow-y:auto; }
    .siparis-list { list-style:none; padding:0; }
    .siparis-list li { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #eee; }

    /* Ödeme Paneli */
    .odeme-panel { width:35%; padding:20px; display:flex; flex-direction:column; }
    .numpad { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin:15px 0; }
    .numpad button { padding:20px; font-size:18px; border:none; border-radius:8px; background:#eee; cursor:pointer; }
    .numpad button:hover { background:#ddd; }
    .quick-btn { background:#36b9cc !important; color:#fff; font-weight:bold; }
    .pay-btn { background:#1cc88a; color:#fff; font-weight:bold; font-size:20px; padding:20px; border-radius:10px; margin-bottom:10px; }
    .cancel-btn { background:#e74a3b; color:#fff; font-weight:bold; font-size:18px; padding:15px; border-radius:10px; }
    .pay-methods { display:flex; gap:10px; margin:10px 0; }
    .pay-methods button { flex:1; padding:15px; border:none; border-radius:8px; font-weight:bold; cursor:pointer; font-size:16px; }
    .method-cash { background:#4e73df; color:#fff; }
    .method-card { background:#f6c23e; color:#000; }
    .method-active { outline:3px solid #000; }

    /* Yazdır butonu */
    .print-btn { background:#f6c23e; color:#000; font-weight:bold; font-size:22px; padding:22px; border:none; border-radius:12px; margin-bottom:15px; cursor:pointer; }
    .print-btn:hover { background:#e0aa2b; }

    /* Bölünmüş ödeme */
    .split-list { list-style:none; padding:0; margin:10px 0; }
    .split-list li { display:flex; justify-content:space-between; align-items:center; padding:6px; border-bottom:1px solid #eee; }
    .split-list button { background:none; border:none; color:red; font-size:18px; cursor:pointer; }

    /* Responsive */
    @media (max-width:1024px) {
      .container { flex-direction:column; height:auto; }
      .masalar-panel, .siparis-panel, .odeme-panel { width:100%; border-right:none; border-bottom:2px solid #ddd; }
      .numpad { grid-template-columns:repeat(3,1fr); }
      .pay-methods { flex-direction:column; }
      .pay-methods button { width:100%; }
    }
  </style>
</head>
<body>
  <header>
    <div>👨‍💼 Lisans: Çıtır Simit 2025091077 <?=$_SESSION['username']?></div>
    <a href="logout.php">Çıkış</a>
  </header>

  <div class="container">
    <!-- Masalar Paneli -->
    <div class="masalar-panel">
      <h2>💺 Masalar</h2>
    <div class="masa-grid">
    <?php while($masa = $masalar->fetch_assoc()) { 
        // Garson.php mantığı ile durum sınıfı belirleme
        $cssClass = "bos";
        if($masa['durum'] == 1){
            if($masa['onayli'] == 1){
                $cssClass = "dolu";      // kırmızı
            } else {
                $cssClass = "bekliyor";  // sarı
            }
        }
    ?>
    <div class="masa <?=$cssClass?>" onclick="loadOrder(<?=$masa['id']?>)">
        Masa <?=$masa['masa_no']?>
    </div>
    <?php } ?>
</div>
    </div>

    <!-- Sipariş Özeti -->
    <div class="siparis-panel">
      <h3>🧾 Sipariş Özeti</h3>
      <div id="orderBox"><p style="color:#666">Bir masa seçiniz.</p></div>
    </div>

    <!-- Ödeme Paneli -->
    <div class="odeme-panel">
      <h3>💳 Ödeme Paneli</h3>
      <div>Ara Toplam: <span id="subTotal">0</span>₺</div>
      <div>KDV (%8): <span id="taxAmount">0</span>₺</div>
      <div><b>Genel Toplam (+KDV): <span id="totalAmount">0</span>₺</b></div>
      <div>İskonto: <span id="discountRate">0</span>%</div>

      <div class="numpad">
        <button onclick="takeAll()" class="quick-btn">Tümü</button>
        <button onclick="applyDiscount()" class="quick-btn">% İskonto</button>
        <button onclick="pressNum('.')">.</button>
        <button onclick="clearInput()">C</button>
        <button onclick="pressNum(1)">1</button>
        <button onclick="pressNum(2)">2</button>
        <button onclick="pressNum(3)">3</button>
        <button onclick="pressNum(4)">4</button>
        <button onclick="pressNum(5)">5</button>
        <button onclick="pressNum(6)">6</button>
        <button onclick="pressNum(7)">7</button>
        <button onclick="pressNum(8)">8</button>
        <button onclick="pressNum(9)">9</button>
        <button onclick="pressNum(0)" style="grid-column: span 2;">0</button>
        <button onclick="backspace()">←</button>
      </div>

      <div class="pay-methods">
        <button id="btnCash" class="method-cash" onclick="setPayMethod('Nakit')">💵 Nakit</button>
        <button id="btnCard" class="method-card" onclick="setPayMethod('Kart')">💳 Kart</button>
      </div>

      <div>Tahsil Edilen: <span id="enteredAmount">0</span>₺</div>
      <button class="quick-btn" style="font-size:20px; padding:20px;" onclick="addSplitPayment()">➕ Ödeme Ekle</button>
      <ul class="split-list" id="splitList"></ul>

      <button class="print-btn" onclick="printReceipt()">🖨 Yazdır</button>
      <button class="pay-btn" id="payBtn" onclick="completePayment()" disabled>✅ ÖDE</button>

      <!-- İptal Butonu -->
      <button class="cancel-btn" onclick="resetPanel()">❌ İptal</button>

      <!-- Yönetim Paneline Giriş Butonu -->
      <div style="margin-top:10px; text-align:center;">
        <a href="admin.php" style="
            display:inline-block;
            background:#4e73df;
            color:#fff;
            font-weight:bold;
            padding:12px 20px;
            border-radius:8px;
            text-decoration:none;
            font-size:16px;
            transition: background 0.2s;
        ">⚙️ Yönetim Paneline Giriş</a>
      </div>

<!-- b16 Bilgilendirme -->
<div style="
    margin-top:15px;
    padding:10px;
    background:#f1f1f1;
    border-radius:8px;
    font-family: Arial, sans-serif;
    font-size:14px;
    color:#333;
    line-height:1.5;
    text-align:left;
">
  <strong>b16 Bilişim-Tasarım-Program</strong><br>
  Selçuk Akın - +90-551-097-21-04<br>
  Niyazi Başköy - +90-555-026-77-82
</div>

<!-- Footer -->
<footer style="
  width:100%;
  text-align:center;
  padding:12px 0;
  background:#4e73df;
  color:#fff;
  font-size:14px;
  border-top:1px solid #2e59d9;
  margin-top:30px;
  clear:both;
">
  © 2025 Çıtır Simit POS Sistemi - Tüm hakları saklıdır.
</footer>
  <script>
function refreshMasalar() {
    fetch("masalar_partial.php") // sadece masaları döndüren PHP
        .then(res => res.text())
        .then(html => {
            document.querySelector(".masa-grid").innerHTML = html;
        })
        .catch(err => console.error(err));
}

// İlk yüklemeden sonra her 10 saniyede bir çağır
setInterval(refreshMasalar, 10000);
    let selectedMasa=null,lastOrder=null,entered="",paymentMethod=null;
    let discountRate=0,hasPrinted=false,splitPayments=[];

    function escapeHtml(t){return t?t.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#039;"):"";}

    function loadOrder(masaId){
  selectedMasa=masaId;
  fetch("order_summary.php?masa="+masaId)
    .then(r=>r.json())
    .then(data=>{
      lastOrder=data;

      // Bekleyen masayı kontrol et
      const masaDiv = document.querySelector(`.masa-grid .masa[onclick='loadOrder(${masaId})']`);
      if(masaDiv && masaDiv.classList.contains('bekliyor')){
          document.getElementById("orderBox").innerHTML="<p style='color:#f39c12'>Bu masa onay bekliyor.</p>";
          document.getElementById("subTotal").innerText="0";
          document.getElementById("totalAmount").innerText="0";
          document.getElementById("taxAmount").innerText="0";
          return;
      }

      if(!data || !data.items || data.items.length===0){
        document.getElementById("orderBox").innerHTML="<p style='color:#999'>Bu masada sipariş yok.</p>";
        document.getElementById("subTotal").innerText="0";
        document.getElementById("totalAmount").innerText="0";
        document.getElementById("taxAmount").innerText="0";
        return;
      }

      let html="<ul class='siparis-list'>";
      data.items.forEach(i=>{html+=`<li><span>${escapeHtml(i.urun)} x ${i.adet}</span><b>${i.tutar}₺</b></li>`;});
      html+="</ul>";
      document.getElementById("orderBox").innerHTML=html;
      updateTotals(data.total);
    })
    .catch(err=>{
      console.error("Hata:",err);
      document.getElementById("orderBox").innerHTML="<p style='color:red'>Sipariş yüklenemedi!</p>";
    });
}

    function updateTotals(total){
      let discount=total*(discountRate/100);
      let newTotal=total-discount;
      if(newTotal<0)newTotal=0;
      let kdv=newTotal*0.08;
      let genel=newTotal+kdv;
      document.getElementById("subTotal").innerText=newTotal.toFixed(2);
      document.getElementById("taxAmount").innerText=kdv.toFixed(2);
      document.getElementById("totalAmount").innerText=genel.toFixed(2);
      document.getElementById("discountRate").innerText=discountRate;
    }

    function pressNum(n){entered+=n;document.getElementById("enteredAmount").innerText=entered;}
    function clearInput(){entered="";document.getElementById("enteredAmount").innerText="0";}
    function backspace(){entered=entered.slice(0,-1);document.getElementById("enteredAmount").innerText=entered||"0";}
    function takeAll(){entered=document.getElementById("totalAmount").innerText;document.getElementById("enteredAmount").innerText=entered;}

    function applyDiscount(){
      let rate=prompt("% İndirim oranı (0-100):",discountRate);
      if(rate===null)return;if(rate.trim()==="")rate=0;
      if(!isNaN(rate)&&rate>=0&&rate<=100){discountRate=parseFloat(rate);updateTotals(lastOrder.total);}else{alert("Geçerli bir oran giriniz.");}
    }

    function setPayMethod(m){
      paymentMethod=m;
      document.getElementById("btnCash").classList.remove("method-active");
      document.getElementById("btnCard").classList.remove("method-active");
      if(m==="Nakit")document.getElementById("btnCash").classList.add("method-active");
      if(m==="Kart")document.getElementById("btnCard").classList.add("method-active");
    }

    function addSplitPayment(){
      let girilen=parseFloat(entered||0);
      if(!girilen||girilen<=0){alert("Ödeme tutarı giriniz.");return;}
      if(!paymentMethod){alert("Ödeme yöntemi seçiniz!");return;}
      splitPayments.push({amount:girilen,method:paymentMethod});
      renderSplitPayments();clearInput();
      updateTotals(lastOrder.total);
    }
    function renderSplitPayments(){
      let list=document.getElementById("splitList");list.innerHTML="";
      splitPayments.forEach((p,idx)=>{
        let li=document.createElement("li");
        li.innerHTML=`${p.amount}₺ - ${p.method} <button onclick="removeSplit(${idx})">❌</button>`;
        list.appendChild(li);
      });
    }
    function removeSplit(i){splitPayments.splice(i,1);renderSplitPayments();updateTotals(lastOrder.total);}

    function printReceipt(){
      if(!lastOrder){alert("Önce masa seçiniz!");return;}
      let sub=parseFloat(document.getElementById("subTotal").innerText);
      let kdv=parseFloat(document.getElementById("taxAmount").innerText);
      let genel=sub+kdv;
      let paidSum=splitPayments.reduce((a,b)=>a+b.amount,0);
      let paraUstu=Math.max(paidSum-genel,0);

      if(paidSum<genel){alert("Yazdırmadan önce toplam ödemeler fiş tutarını karşılamalı!");return;}

      let now=new Date();
      let tarih=now.toLocaleDateString("tr-TR");
      let saat=now.toLocaleTimeString("tr-TR");

      let html=`<html><head><title>Fiş Yazdır</title>
      <style>
        @page { size: 80mm auto; margin: 0; }
        body{font-family:monospace;font-size:12px;padding:5px;width:80mm;margin:0 auto;}
        h2{text-align:center;margin:5px 0;font-size:14px;}
        .line{border-top:1px dashed #000;margin:4px 0;}
        .center{text-align:center;}
      </style></head><body>
      <h2>POS FİŞİ</h2>
      <div class="center">${tarih} ${saat}</div>
      Masa No: ${selectedMasa}<br>
      <div class="line"></div>
      ${lastOrder.items.map(i=>`${i.urun} x ${i.adet} .... ${i.tutar}₺`).join("<br>")}
      <div class="line"></div>
      Ara Toplam: ${sub.toFixed(2)}₺<br>
      KDV (%8): ${kdv.toFixed(2)}₺<br>
      <b>GENEL TOPLAM: ${genel.toFixed(2)}₺</b><br>
      İskonto: ${discountRate}%<br>
      <div class="line"></div>
      <b>ÖDEMELER:</b><br>
      ${splitPayments.map(p=>`${p.amount}₺ - ${p.method}`).join("<br>")}
      <div class="line"></div>
      ${paraUstu>0?`<b>Para Üstü: ${paraUstu.toFixed(2)}₺</b><br>`:""}
      <p style="text-align:center;margin-top:10px">Teşekkür ederiz!</p>
      </body></html>`;

      let win=window.open("","PRINT","height=600,width=400");
      win.document.write(html);win.document.close();win.focus();win.print();win.close();
      hasPrinted=true;document.getElementById("payBtn").disabled=false;
    }

    function completePayment(){
      if(!lastOrder){alert("Masa seçiniz!");return;}
      if(splitPayments.length===0){alert("Ödeme eklenmedi!");return;}
      if(!hasPrinted){alert("Lütfen önce fişi yazdırınız!");return;}

      fetch("odeme_kapat.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({masa_id: selectedMasa,payments: splitPayments})
      })
      .then(r=>r.text())
      .then(res=>{
        if(res==="success"){
          alert("Ödeme tamamlandı ✅");
          resetPanel();location.reload();
        } else {alert("Hata: "+res);}
      });
    }

    function resetPanel(){
      selectedMasa=null;lastOrder=null;entered="";paymentMethod=null;discountRate=0;hasPrinted=false;splitPayments=[];
      document.getElementById("orderBox").innerHTML="<p style='color:#666'>Bir masa seçiniz.</p>";
      document.getElementById("subTotal").innerText="0";
      document.getElementById("totalAmount").innerText="0";
      document.getElementById("taxAmount").innerText="0";
      document.getElementById("discountRate").innerText="0";
      document.getElementById("enteredAmount").innerText="0";
      document.getElementById("btnCash").classList.remove("method-active");
      document.getElementById("btnCard").classList.remove("method-active");
      document.getElementById("splitList").innerHTML="";
      document.getElementById("payBtn").disabled=true;
    }
  </script>
  
</body>
</html>
</body>
</html>