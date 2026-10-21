<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#2b3fd6">
<title>Matekrajt – 7. és 8. osztályosoknak</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Nunito:wght@600;800&display=swap" rel="stylesheet">
<style>
:root{--bg:#2b3fd6;--ink:#14143a;--sun:#ffd23f;--pink:#ff5c7a;--ok:#2ecc71;--card:#fff;box-sizing:border-box}
*,*::before,*::after{box-sizing:inherit}
html,body{height:100%;margin:0}
body{background:var(--bg);color:var(--ink);font-family:Nunito,system-ui,sans-serif;font-weight:600;
 padding:env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);
 -webkit-tap-highlight-color:transparent;user-select:none;touch-action:manipulation}
#app{max-width:640px;min-height:100%;margin:0 auto;padding:16px;display:flex;flex-direction:column;justify-content:center;gap:16px}
h1,.big{font-family:"Lilita One",Impact,sans-serif;font-weight:400;margin:0;color:#fff;line-height:1}
h1{font-size:clamp(3rem,13vw,5.5rem);transform:rotate(-3deg);text-shadow:4px 4px 0 var(--ink)}
h1 span{color:var(--sun)}
p{margin:0;color:#fff;font-size:clamp(1.05rem,3.2vw,1.35rem)}
button{font:inherit;font-weight:800;border:0;border-radius:18px;cursor:pointer;color:var(--ink);background:var(--sun);
 box-shadow:0 6px 0 var(--ink);padding:18px;font-size:clamp(1.2rem,4vw,1.6rem);transition:transform .08s,box-shadow .08s}
button:active{transform:translateY(5px);box-shadow:0 1px 0 var(--ink)}
button:focus-visible{outline:4px solid #fff;outline-offset:3px}
.row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.hide{display:none!important}
.best{font-size:.95rem;font-weight:800;display:block;opacity:.75}
/* játék */
.top{display:flex;justify-content:space-between;align-items:center;color:#fff;font-weight:800;font-size:clamp(1rem,3.5vw,1.3rem)}
.hearts{letter-spacing:4px}
.bar{height:14px;background:rgba(0,0,0,.3);border-radius:9px;overflow:hidden}
.bar i{display:block;height:100%;width:100%;background:var(--sun);transform-origin:left;transition:background .3s}
.q{background:var(--card);border-radius:24px;padding:26px 20px;min-height:150px;display:flex;align-items:center;justify-content:center;
 text-align:center;font-family:"Lilita One",sans-serif;font-size:clamp(1.7rem,6vw,2.8rem);line-height:1.25;box-shadow:0 8px 0 var(--ink)}
.combo{height:1.6em;text-align:center;color:var(--sun);font-family:"Lilita One",sans-serif;font-size:1.4rem}
.ans button{background:#fff;font-family:"Lilita One",sans-serif;font-weight:400;font-size:clamp(1.5rem,5.5vw,2.3rem);padding:22px 8px}
.ans button.good{background:var(--ok);color:#fff}
.ans button.bad{background:var(--pink);color:#fff;animation:shake .35s}
@keyframes shake{25%{transform:translateX(-8px)}75%{transform:translateX(8px)}}
.end .big{font-size:clamp(2.4rem,9vw,4rem)}
.score{font-family:"Lilita One";color:var(--sun);font-size:clamp(4rem,18vw,7rem);line-height:1}
@media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
</style>
</head>
<body>
<main id="app">

<section id="start" class="hide">
 <h1>Matek<span>rajt</span></h1>
 <p style="margin:18px 0 22px">10 kérdés, 3 élet, 15 másodperc kérdésenként. Minél gyorsabb vagy, annál több pontot kapsz!</p>
 <div class="row">
  <button data-g="7">7. osztály<span class="best" id="b7"></span></button>
  <button data-g="8">8. osztály<span class="best" id="b8"></span></button>
 </div>
</section>

<section id="game" class="hide">
 <div class="top"><span class="hearts" id="hearts"></span><span id="num"></span><span id="pts"></span></div>
 <div class="bar" style="margin:12px 0 16px"><i id="t"></i></div>
 <div class="q" id="q"></div>
 <div class="combo" id="combo"></div>
 <div class="row ans" id="ans"></div>
</section>

<section id="end" class="end hide" style="text-align:center">
 <div class="big" id="medal"></div>
 <div class="score" id="final"></div>
 <p id="msg" style="margin:10px 0 22px"></p>
 <div class="row">
  <button id="again">Újra</button>
  <button id="menu" style="background:#fff">Menü</button>
 </div>
</section>

</main>
<script>
const $=id=>document.getElementById(id), R=(a,b)=>Math.floor(Math.random()*(b-a+1))+a;
const s=n=>n<0?`(−${-n})`:n;
const T=15, N=10;

const G={
 7:[
  ()=>{const a=R(-12,12),b=R(-12,12);return[`${s(a)} + ${s(b)} = ?`,a+b]},
  ()=>{const a=R(-12,12),b=R(-12,12);return[`${s(a)} − ${s(b)} = ?`,a-b]},
  ()=>{const a=R(-9,9)||3,b=R(2,9);return[`${s(a)} · ${b} = ?`,a*b]},
  ()=>{const p=[10,20,25,50][R(0,3)],n=R(1,10)*20;return[`Mennyi a(z) ${n} ${p}%-a?`,n*p/100]},
  ()=>{const x=R(2,12),m=R(2,9),c=R(1,15);return[`${m}x + ${c} = ${m*x+c}<br>x = ?`,x]},
  ()=>{const a=R(3,15),b=R(3,15);return[`Egy téglalap oldalai ${a} cm és ${b} cm. Mekkora a kerülete (cm)?`,2*(a+b)]},
  ()=>{const a=R(3,15),b=R(3,15);return[`Egy téglalap oldalai ${a} cm és ${b} cm. Mekkora a területe (cm²)?`,a*b]}
 ],
 8:[
  ()=>{const n=R(3,16);return[`√${n*n} = ?`,n]},
  ()=>{const b=R(2,5),e=R(2,4);return[`${b}<sup>${e}</sup> = ?`,b**e]},
  ()=>{const [a,b,c]=[[3,4,5],[5,12,13],[6,8,10],[8,15,17],[9,12,15]][R(0,4)];return[`Egy derékszögű háromszög befogói ${a} és ${b}. Mekkora az átfogó?`,c]},
  ()=>{const x=R(1,10),a=R(3,8),c=R(1,a-1),b=R(1,10);return[`${a}x + ${b} = ${c}x + ${a*x+b-c*x}<br>x = ?`,x]},
  ()=>{const n=R(2,10)*100,p=[10,20,50][R(0,2)];return[`Egy ${n} Ft-os termék ára ${p}%-kal nő. Mennyi lesz az új ár (Ft)?`,n*(100+p)/100]},
  ()=>{const a=R(-9,-2),b=R(-9,-2);return[`${s(a)} · ${s(b)} − 4 = ?`,a*b-4]},
  ()=>{const a=R(2,9);return[`(x + ${a})² = x² + ◻·x + ${a*a}<br>◻ = ?`,2*a]}
 ]
};

let grade,qi,lives,score,streak,ans,tm,t0,locked;
const show=id=>["start","game","end"].forEach(x=>$(x).classList.toggle("hide",x!==id));
const best=g=>{try{return +localStorage.getItem("mr"+g)||0}catch(e){return 0}};
const save=(g,v)=>{try{localStorage.setItem("mr"+g,v)}catch(e){}};

function menu(){
 [7,8].forEach(g=>$("b"+g).textContent=best(g)?`Rekord: ${best(g)}`:"Még nincs rekord");
 show("start");
}
function start(g){grade=g;qi=0;lives=3;score=0;streak=0;show("game");next()}

function next(){
 if(qi>=N||lives<=0)return finish();
 qi++;locked=false;
 const [q,a]=G[grade][R(0,G[grade].length-1)]();
 ans=a;
 const opts=new Set([a]);
 while(opts.size<4){let d=a+R(-6,6)+(R(0,2)?0:R(-1,1)*10);if(d!==a)opts.add(d)}
 $("q").innerHTML=q;
 $("ans").innerHTML="";
 [...opts].sort(()=>Math.random()-.5).forEach(o=>{
  const b=document.createElement("button");b.textContent=String(o).replace("-","−");b.onclick=()=>pick(b,o);$("ans").append(b);
 });
 $("hearts").textContent="❤️".repeat(lives)+"🖤".repeat(3-lives);
 $("num").textContent=`${qi}/${N}`;$("pts").textContent=`⭐ ${score}`;
 $("combo").textContent=streak>=2?`🔥 ${streak} egymás után!`:"";
 clearInterval(tm);t0=Date.now();
 const bar=$("t");bar.style.background="var(--sun)";
 tm=setInterval(()=>{
  const left=T-(Date.now()-t0)/1000;
  bar.style.transform=`scaleX(${Math.max(left,0)/T})`;
  if(left<5)bar.style.background="var(--pink)";
  if(left<=0)pick(null,null);
 },100);
}

function pick(btn,val){
 if(locked)return;locked=true;clearInterval(tm);
 const left=Math.max(0,T-(Date.now()-t0)/1000);
 const ok=val===ans;
 [...$("ans").children].forEach(b=>{if(b.textContent.replace("−","-")==String(ans))b.classList.add("good")});
 if(ok){streak++;score+=10+Math.round(left)+Math.min(streak-1,5)*2}
 else{streak=0;lives--;if(btn)btn.classList.add("bad")}
 $("pts").textContent=`⭐ ${score}`;
 $("hearts").textContent="❤️".repeat(lives)+"🖤".repeat(3-lives);
 setTimeout(next,ok?700:1400);
}

function finish(){
 const rec=score>best(grade);if(rec)save(grade,score);
 $("final").textContent=score;
 $("medal").textContent=lives<=0?"Jaj, elfogyott az élet!":score>=160?"🏆 Bajnok!":score>=100?"🥈 Ügyes vagy!":"🥉 Jó kezdés!";
 $("msg").textContent=rec?"Új rekord! 🎉":`Rekordod: ${best(grade)}`;
 show("end");
}

document.querySelectorAll("[data-g]").forEach(b=>b.onclick=()=>start(+b.dataset.g));
$("again").onclick=()=>start(grade);
$("menu").onclick=menu;
menu();
</script>
</body>
</html>
