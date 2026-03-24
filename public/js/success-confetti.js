/**
 * DMF Dental Training Center — Success Page Confetti
 * success-confetti.js
 */
(function () {
    const colors = ['#0d8de8', '#7cc5fb', '#e0effe', '#f0f7ff', '#fff'];
    const canvas = document.createElement('canvas');
    canvas.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;pointer-events:none;z-index:9999;';
    document.body.appendChild(canvas);
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const pieces = Array.from({ length: 80 }, () => ({
        x: Math.random() * canvas.width,
        y: -20 - Math.random() * 200,
        r: 4 + Math.random() * 5,
        d: 2 + Math.random() * 3,
        color: colors[Math.floor(Math.random() * colors.length)],
        tilt: Math.random() * 10 - 5,
        tiltAngle: 0,
        tiltSpeed: 0.1 + Math.random() * 0.1,
    }));

    let frame = 0;
    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        pieces.forEach(p => {
            p.tiltAngle += p.tiltSpeed;
            p.y += (Math.cos(frame / 25) + p.d) * 0.9;
            p.x += Math.sin(frame / 25) * 0.8;
            p.tilt = Math.sin(p.tiltAngle) * 10;
            ctx.globalAlpha = Math.max(0, 1 - frame / 140);
            ctx.fillStyle = p.color;
            ctx.beginPath();
            ctx.ellipse(p.x, p.y, p.r, p.r / 2, p.tilt, 0, 2 * Math.PI);
            ctx.fill();
        });
        frame++;
        if (frame < 150) requestAnimationFrame(draw);
        else canvas.remove();
    }
    draw();
})();
