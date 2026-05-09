const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");
const replayBtn = document.getElementById("replayBtn");

let DPR = Math.min(window.devicePixelRatio || 1, 2);

function resizeCanvas() {
    DPR = Math.min(window.devicePixelRatio || 1, 2);

    canvas.width = window.innerWidth * DPR;
    canvas.height = window.innerHeight * DPR;

    canvas.style.width = window.innerWidth + "px";
    canvas.style.height = window.innerHeight + "px";

    ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
}

resizeCanvas();
window.addEventListener("resize", resizeCanvas);
// ===== MATRIX DATA =====
const matrix1 = [
"0000000000000000000000",
"0000000000000000000000",
"0000000100000000000000",
"0000001010000000000000",
"0000000000000000000000",
"0111001110010001001110",
"1000010001011001010000",
"1000010001010101010011",
"1000010001010011010001",
"0111001110010001001110",
"0000000000000000000000",
"0000000000000000000000",
"0000000000000000000000",
"0000000000000000000000",
"0000000000000010000000",
"0000000000000100000000",
"0000000000000000000000",
"0111010001010001001110",
"1000010001010001010001",
"1000011111010001010001",
"1000010001010001011111",
"0111010001001110010001",
"0000000000000000000000",
];

const matrix2 = [
"0000000000000000000000000000",
"0000000000000000000000000000",
"0000000000000000000000000000",
"0000000000000000000000000000",
"0001111000111000111001000100",
"0001000101000101000101100100",
"0011100101000101000101010100",
"0001000101000101111101001100",
"0001111000111001000101000100",
"0000000000000000000000000000",
"0000000000000000000000000000",
"0000000000000000000000000000",
"0000000000000000000000000000",
"1111101110001110010001001110",
"0010001001010001011001010000",
"0010001111010001010101010011",
"0010001010011111010011010001",
"0010001001010001010001001110",
"0000000000000000000000000000",
];

const matrix3 = [
"00000000000000000000000",
"00000000000000000110000",
"00000000000000000010000",
"00000000000000000100000",
"00010001001110010001000",
"00011001010000010001000",
"00010101010011010001000",
"00010011010001010001000",
"00010001001110001110000",
"00000000000000000000000",
"00000000000000000000000",
"00000000000000000000000",
"00000000000000000000000",
"10001001110001110010001",
"11001010000010001011001",
"10101010011010001010101",
"10011010001010001010011",
"10001001110001110010001",
"00000000000000000000000",
];

const matrices = [matrix1, matrix2, matrix3];

const flower = "🌷";

let particles = [];
let currentMatrix = 0;
let phase = 0;
let phaseStart = performance.now();
let stopped = false;

const FORM_DURATION = 1800;
const HOLD_DURATION = 3000;
const BURST_DURATION = 1300;

const isMobile = window.innerWidth < 768;
const cellSize = isMobile ? 10 : 18;

function ease(t){
    return 1 - Math.pow(1 - t, 3);
}

function sendTrack(type) {
    fetch("track.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `type=${type}`
    });
}

function buildParticles(matrix){
    particles = [];

    const rows = matrix.length;
    const cols = matrix[0].length;

    const width = cols * cellSize;
    const height = rows * cellSize;

    const offsetX = (window.innerWidth - width) / 2;
    const offsetY = (window.innerHeight - height) / 2;

    for(let y = 0; y < rows; y++){
        for(let x = 0; x < cols; x++){
            if(matrix[y][x] === "1"){
                particles.push({
                    startX: Math.random() * window.innerWidth,
                    startY: Math.random() * window.innerHeight,
                    targetX: offsetX + x * cellSize,
                    targetY: offsetY + y * cellSize,
                    endX: Math.random() * window.innerWidth,
                    endY: Math.random() * window.innerHeight,
                    size: cellSize
                });
            }
        }
    }
}

function drawParticle(x, y, size){
    ctx.font = `${size}px serif`;
    ctx.fillText(flower, x, y);
}

function restartAnimation(){
    sendTrack("replay");

    currentMatrix = 0;
    phase = 0;
    stopped = false;
    phaseStart = performance.now();

    buildParticles(matrices[0]);
    requestAnimationFrame(animate);
}

function nextPhase(){
    phase++;

    if(currentMatrix === matrices.length - 1){
        if(phase >= 1){
            stopped = true;
            return;
        }
    }

    if(phase > 2){
        currentMatrix++;
        phase = 0;
        buildParticles(matrices[currentMatrix]);
    }

    phaseStart = performance.now();
}

function drawStatic(){
    ctx.clearRect(0, 0, window.innerWidth, window.innerHeight);
    particles.forEach(p => drawParticle(p.targetX, p.targetY, p.size));
}

function animate(now){

    if(stopped){
        drawStatic();
        return;
    }

    ctx.clearRect(0, 0, window.innerWidth, window.innerHeight);

    let elapsed = now - phaseStart;

    if(
        (phase === 0 && elapsed > FORM_DURATION) ||
        (phase === 1 && elapsed > HOLD_DURATION) ||
        (phase === 2 && elapsed > BURST_DURATION)
    ){
        nextPhase();
        elapsed = 0;
    }

    particles.forEach(p => {

        let x, y;

        if(phase === 0){
            const t = ease(elapsed / FORM_DURATION);
            x = p.startX + (p.targetX - p.startX) * t;
            y = p.startY + (p.targetY - p.startY) * t;
        }
        else if(phase === 1){
            x = p.targetX;
            y = p.targetY;
        }
        else{
            const t = ease(elapsed / BURST_DURATION);
            x = p.targetX + (p.endX - p.targetX) * t;
            y = p.targetY + (p.endY - p.targetY) * t;
        }

        drawParticle(x, y, p.size);
    });

    requestAnimationFrame(animate);
}

replayBtn.addEventListener("click", restartAnimation);

window.addEventListener("beforeunload", () => {
    navigator.sendBeacon("track.php");
});

buildParticles(matrices[0]);
requestAnimationFrame(animate);