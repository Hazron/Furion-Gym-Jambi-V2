document.addEventListener("DOMContentLoaded",function(){let r=[],m=null;const d=e=>new Intl.NumberFormat("id-ID",{style:"currency",currency:"IDR",minimumFractionDigits:0}).format(e);window.toggleModal=function(e,t){const o=document.getElementById(e);t?o.classList.remove("hidden"):o.classList.add("hidden")},window.previewImage=function(e){const t=document.getElementById("upload-placeholder"),o=document.getElementById("image-preview");if(e.files&&e.files[0]){const n=new FileReader;n.onload=function(a){o.src=a.target.result,o.classList.remove("hidden"),t.classList.add("opacity-0")},n.readAsDataURL(e.files[0])}},window.toggleDropdown=function(){document.getElementById("memberDropdownList").classList.toggle("hidden")},window.selectMember=function(e,t){document.getElementById("selectedMemberDisplay").value=e,m=t,document.getElementById("memberDropdownList").classList.add("hidden")},window.filterMembers=function(){let e=document.getElementById("memberSearchInput").value.toUpperCase(),o=document.getElementById("memberList").getElementsByTagName("li");for(let n=0;n<o.length;n++)(o[n].textContent||o[n].innerText).toUpperCase().indexOf(e)>-1?o[n].style.display="":o[n].style.display="none"},window.tambahKeKeranjang=function(e){const t=e.dataset.id,o=e.dataset.name,n=parseInt(e.dataset.price),a=e.dataset.image;if(!t)return;const i={id:String(t),name:o,price:n,image:a,qty:1},l=r.findIndex(s=>s.id===i.id);l>-1?r[l].qty+=1:r.push(i),u()};function u(){const e=document.getElementById("cart-items-container"),t=document.getElementById("total-payment"),o=document.getElementById("subtotal-display");e.innerHTML="";let n=0;if(r.length===0){e.innerHTML=`
                <div class="h-full flex flex-col items-center justify-center text-gray-400 space-y-3 opacity-60">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <p class="text-sm">Keranjang kosong</p>
                </div>`,t&&(t.innerText=d(0)),o&&(o.innerText=d(0));return}r.forEach(a=>{const i=a.price*a.qty;n+=i;const l=`
                <div class="flex items-center justify-between mb-4 p-2 border-b border-gray-100 bg-white rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 border border-gray-200">
                            ${a.image?`<img src="${a.image}" class="w-full h-full object-cover">`:'<div class="w-full h-full bg-gray-200"></div>'}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="text-sm font-bold text-gray-800 line-clamp-1">${a.name}</h5>
                            <p class="text-xs text-gray-500">${d(a.price)}</p>
                            <div class="flex items-center mt-1 gap-2">
                                <button onclick="updateQty('${a.id}', -1)" class="w-6 h-6 bg-gray-100 rounded text-sm hover:bg-gray-200 font-bold">-</button>
                                <span class="text-xs font-bold w-6 text-center">${a.qty}</span>
                                <button onclick="updateQty('${a.id}', 1)" class="w-6 h-6 bg-gray-100 rounded text-sm hover:bg-gray-200 font-bold">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="text-right flex flex-col justify-between h-full pl-2">
                        <p class="text-sm font-bold text-blue-600">${d(i)}</p>
                        <button onclick="removeItem('${a.id}')" class="text-gray-400 hover:text-red-500 self-end mt-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            `;e.insertAdjacentHTML("beforeend",l)}),t&&(t.innerText=d(n)),o&&(o.innerText=d(n))}window.updateQty=function(e,t){const o=r.findIndex(n=>n.id===String(e));o>-1&&(r[o].qty+t>0&&(r[o].qty+=t),u())},window.removeItem=function(e){const t=r.findIndex(o=>o.id===String(e));t>-1&&(r.splice(t,1),u())},window.toggleBuktiTransfer=function(e){const t=document.getElementById("swal-bukti-transfer-container");e==="transfer"||e==="qris"?t.classList.remove("hidden"):(t.classList.add("hidden"),document.getElementById("swal-bukti-transfer").value="")},window.processPayment=function(){if(r.length===0){Swal.fire({icon:"warning",title:"Keranjang Kosong",text:"Silahkan pilih produk."});return}const e=document.getElementById("selectedMemberDisplay").value;let t=0,o='<div class="text-left text-sm space-y-2 mb-4 max-h-60 overflow-y-auto bg-gray-50 p-3 rounded-lg border border-gray-200">';r.forEach(n=>{const a=n.price*n.qty;t+=a,o+=`
        <div class="flex justify-between border-b border-gray-200 pb-1 last:border-0">
            <div>
                <div class="font-bold text-gray-700">${n.name}</div>
                <div class="text-xs text-gray-500">${n.qty} x ${d(n.price)}</div>
            </div>
            <div class="font-semibold text-gray-800">${d(a)}</div>
        </div>`}),o+="</div>",Swal.fire({title:"Konfirmasi Pembayaran",html:`
            <div class="text-left mb-3">
                <p class="text-xs font-bold text-gray-400 uppercase">Pembeli</p>
                <p class="text-base font-bold text-blue-600">${e}</p>
            </div>
            ${o}
            <div class="flex justify-between items-center border-t border-dashed border-gray-300 pt-3 mt-2 mb-4">
                <span class="font-bold text-gray-700">Total Akhir:</span>
                <span class="font-bold text-xl text-blue-600">${d(t)}</span>
            </div>
            <div class="text-left">
                <label class="text-xs font-bold text-gray-500 uppercase">Metode Pembayaran</label>
                <select id="swal-payment-method" onchange="toggleBuktiTransfer(this.value)" class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="cash">Tunai (Cash)</option>
                    <option value="qris">QRIS / E-Wallet</option>
                    <option value="transfer">Transfer Bank</option>
                </select>
            </div>

            <div id="swal-bukti-transfer-container" class="text-left mt-3 hidden bg-blue-50 p-3 rounded-lg border border-blue-100">
                <label class="text-xs font-bold text-blue-700 uppercase">Upload Bukti Transfer</label>
                <input type="file" id="swal-bukti-transfer" accept="image/*,.pdf" class="w-full mt-1 text-sm bg-white border border-blue-200 rounded text-gray-600 p-1.5 focus:outline-none">
            </div>

            <div class="text-left mt-3">
                <label class="text-xs font-bold text-gray-500 uppercase">Status Pembayaran</label>
                <select id="swal-payment-status" class="w-full mt-1 p-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none text-green-600 bg-green-50">
                    <option value="paid" class="text-green-600 font-bold">LUNAS (Paid)</option>
                    <option value="pending" class="text-yellow-600 font-bold">BELUM LUNAS (Pending)</option>
                </select>
            </div>
        `,icon:"question",showCancelButton:!0,confirmButtonText:"Bayar Sekarang",cancelButtonText:"Batal",confirmButtonColor:"#2563EB",allowOutsideClick:!1,preConfirm:()=>{const n=document.getElementById("swal-payment-method").value,a=document.getElementById("swal-bukti-transfer");return n==="transfer"&&a.files.length===0?(Swal.showValidationMessage("Bukti transfer wajib diupload!"),!1):{method:n,status:document.getElementById("swal-payment-status").value,buktiFile:a.files[0]}}}).then(n=>{n.isConfirmed&&b(e,t,n.value.method,n.value.status,n.value.buktiFile)})};function b(e,t,o,n,a){const i=document.querySelector('meta[name="csrf-token"]');if(!i){Swal.fire("Error","CSRF token tidak ditemukan.","error");return}Swal.fire({title:"Memproses Transaksi...",html:"Mohon tunggu, sistem sedang menyimpan data.",allowOutsideClick:!1,didOpen:()=>Swal.showLoading()});const l=new FormData;l.append("member_id",m||""),l.append("total_amount",t),l.append("payment_method",o),l.append("payment_status",n),r.forEach((s,c)=>{l.append(`items[${c}][id]`,s.id),l.append(`items[${c}][qty]`,s.qty)}),a&&l.append("bukti_transfer",a),fetch(window.routes.simpanTransaksi,{method:"POST",headers:{"X-CSRF-TOKEN":i.getAttribute("content")},body:l}).then(s=>s.ok?s.json():s.text().then(c=>{throw new Error(c)})).then(s=>{s.status==="success"?Swal.fire({icon:"success",title:"Transaksi Sukses!",text:`Inv: ${s.invoice} | Status: ${n.toUpperCase()}`,timer:2500,showConfirmButton:!1}).then(()=>{r=[],window.location.reload()}):Swal.fire("Gagal!",s.message,"error")}).catch(s=>{console.error("Error:",s),Swal.fire("Server Error","Cek Console untuk detail.","error")})}const f=document.getElementById("formTambahProduk");f&&f.addEventListener("submit",function(e){e.preventDefault(),Swal.fire({title:"Menyimpan Produk...",text:"Mohon tunggu sebentar.",allowOutsideClick:!1,didOpen:()=>Swal.showLoading()}),this.submit()}),window.openEditModal=function(e){document.getElementById("edit_nama_produk").value=e.nama_produk,document.getElementById("edit_harga_produk").value=e.harga_produk,document.getElementById("edit_stok_produk").value=e.stok_produk,document.getElementById("calc_tambah_stok").value="";const t=document.getElementById("edit_preview_img");e.gambar_produk?t.src=window.routes.assetProduk+e.gambar_produk:t.src="https://via.placeholder.com/150?text=No+Img";const o=e.id_produk||e.id;document.getElementById("formEditProduk").action=window.routes.baseProduk+"/"+o,window.toggleModal("modalEditProduk",!0)};const g=document.getElementById("formEditProduk");g&&g.addEventListener("submit",function(e){e.preventDefault(),Swal.fire({title:"Menyimpan Perubahan...",text:"Mohon tunggu, sedang mengupdate data.",allowOutsideClick:!1,didOpen:()=>Swal.showLoading()}),this.submit()});const p=document.getElementById("calc_tambah_stok");p&&p.addEventListener("input",function(){}),window.hapusProduk=function(e,t){},window.toggleStatus=function(e,t){const o=t==="active"?"menonaktifkan":"mengaktifkan",n=t==="active"?"#d33":"#10b981";Swal.fire({title:"Ubah Status?",text:`Anda ingin ${o} produk ini?`,icon:"question",showCancelButton:!0,confirmButtonColor:n,confirmButtonText:"Ya, Lakukan!",cancelButtonText:"Batal"}).then(a=>{a.isConfirmed&&(Swal.fire({title:"Memproses...",didOpen:()=>Swal.showLoading()}),fetch(window.routes.baseProduk+"/"+e+"/status",{method:"PATCH",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').content}}).then(i=>i.json()).then(i=>{i.status==="success"?Swal.fire({icon:"success",title:"Berhasil!",text:i.message,timer:1500,showConfirmButton:!1}).then(()=>window.location.reload()):Swal.fire("Gagal!",i.message,"error")}).catch(i=>Swal.fire("Error","Terjadi kesalahan sistem.","error")))})}});
