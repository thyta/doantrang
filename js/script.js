const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");

const warning = document.getElementById("rotateWarning");

// ===== MOBILE DETECT =====
function isMobile() {
    return /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
}

let isMobileDevice = isMobile();

// ===== ORIENTATION CHECK =====
function checkOrientation() {
    if (!isMobileDevice) return;

    if (window.innerHeight > window.innerWidth) {
        warning.style.display = "flex";
    } else {
        warning.style.display = "none";
    }
}

window.addEventListener("resize", checkOrientation);
window.addEventListener("orientationchange", checkOrientation);
checkOrientation();

// ===== CANVAS RESIZE =====
function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}
resizeCanvas();
window.addEventListener("resize", resizeCanvas);

// ===== MATRIX 1 =====
const matrix1 = [
"00000000100000000000000000000000000000000010000000",
"00000001010000000000000000000000000000000100000000",
"00000000000000000000000000000000000000000000000000",
"00111001111010001001110000001111010001010001001110",
"01000010001011001010000000010000010001010001010001",
"01000010001010101010011000010000011111010001010001",
"01000010001010011010001000010000010001010001011111",
"00111001110010001001110000001111010001001110010001",
"00000000000000000000000000000000000000000000000000",
"00000000000000000000000000000000000000000000000000",
"00000000000000000000000000000000000000000000000000",
"00000000110000000000000000000000000110000000000000",
"00000000010000000000000000000000000010000000000000",
"00000000100000000000000000000000000100000000000000",
"00000000000000000000000000000000000000000000000000",
"01110001110010001001110000001110001110010001010001",
"01001010001011001010000000001001010001011001010001",
"11101010001010101010011000011101010001010101011111",
"01001010001010011010001000001001011111010011010001",
"01110001110010001001110000001110010001010001010001",
];

// ===== MATRIX 2 =====
const matrix2 = [
"00000000000000000000000000000000000000000000000000000000",
"00000000000000000000000000000000000000000000000000000000",
"00000000000000000000000000000000000000000000000000000000",
"00000000000000000000000000000000000000000000000000000000",
"01110001110001110010001000011111011110001110010001001110",
"01001010001010001011001000000100010001010001011001010000",
"11101010001010001010101000000100011110010001010101010111",
"01001010001011111010011000000100010100011111010011010001",
"01110001110010001010001000000100010010010001010001001110",
];

// ===== MATRIX 3 =====
const matrix3 = [
"000000000000000000110000000000000000000000000000000000",
"000000000000000000010000000000000000000000000000000000",
"000000000000000000100000000000000000000000000000000000",
"000000000000000000000000000000000000000000000000000000",
"100001001111000100001000000100001001111000111100100001",
"110001010000000100001000000110001010000001000010110001",
"101001010000000100001000000101001010000001000010101001",
"100101010011100100001000000100101010011101000010100101",
"100011010000100100001000000100011010000101000010100011",
"100001001111000011110000000100001001111000111100100001",
];

const matrices = [matrix1, matrix2, matrix3];
const flower = "🌷";

// ===== STATE =====
let particles = [];
let currentMatrix = 0;
let phase = 0;
let phaseStart = performance.now();
let stopped = false;

const FORM_DURATION = 2000;
const HOLD_DURATION = 2000;
const BURST_DURATION = 1500;

// ===== EASING =====
function ease(t){
    return 1 - Math.pow(1 - t, 3);
}

// ===== BUILD PARTICLES =====
function buildParticles(matrix){
    particles = [];

    const cell = window.innerWidth < 768 ? 12 : 18;
    const totalWidth = matrix[0].length * cell;
    const totalHeight = matrix.length * cell;

    const offsetX = (canvas.width - totalWidth) / 2;
    const offsetY = (canvas.height - totalHeight) / 2;

    for(let y=0;y<matrix.length;y++){
        for(let x=0;x<matrix[y].length;x++){
            if(matrix[y][x]==="1"){
                particles.push({
                    startX: Math.random()*canvas.width,
                    startY: Math.random()*canvas.height,
                    targetX: offsetX + x*cell,
                    targetY: offsetY + y*cell,
                    endX: Math.random()*canvas.width,
                    endY: Math.random()*canvas.height,
                    size: cell
                });
            }
        }
    }
}

buildParticles(matrices[0]);

// ===== NEXT PHASE =====
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

// ===== STATIC DRAW =====
function drawStatic(){
    ctx.clearRect(0,0,canvas.width,canvas.height);

    particles.forEach(p=>{
        ctx.font = `${p.size}px Arial`;
        ctx.fillText(flower, p.targetX, p.targetY);
    });
}

// ===== ANIMATE =====
function animate(now){

    if(stopped){
        drawStatic();
        return;
    }

    ctx.clearRect(0,0,canvas.width,canvas.height);

    let rotated = false;

    // ===== ROTATE ON MOBILE LANDSCAPE =====
    if (isMobileDevice && window.innerHeight < window.innerWidth) {
        ctx.save();
        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate(Math.PI / 2);
        ctx.translate(-canvas.height / 2, -canvas.width / 2);
        rotated = true;
    }

    let elapsed = now - phaseStart;

    if(
        (phase===0 && elapsed>FORM_DURATION) ||
        (phase===1 && elapsed>HOLD_DURATION) ||
        (phase===2 && elapsed>BURST_DURATION)
    ){
        nextPhase();
        elapsed = 0;
    }

    particles.forEach(p=>{

        let x,y;

        if(phase===0){
            const t = ease(elapsed/FORM_DURATION);
            x = p.startX + (p.targetX-p.startX)*t;
            y = p.startY + (p.targetY-p.startY)*t;
        }
        else if(phase===1){
            x = p.targetX;
            y = p.targetY;
        }
        else{
            const t = ease(elapsed/BURST_DURATION);
            x = p.targetX + (p.endX-p.targetX)*t;
            y = p.targetY + (p.endY-p.targetY)*t;
        }

        ctx.font = `${p.size}px Arial`;
        ctx.fillText(flower,x,y);
    });

    if(rotated){
        ctx.restore();
    }

    requestAnimationFrame(animate);
}

requestAnimationFrame(animate);