let context = null;
let clic = { x: 0, y: 0 };

let plateau = new Plan();
plateau.creerVehicule(1)

let time = new Timer(50,90,0)

plateau.creerCheckpoint();

let start = new Image()
start.src = "Start.png"

let victori = new Image()
victori.src = "Victoire.png"

function init() {
    context = document.getElementById("cvs").getContext("2d");
    context.width = document.getElementById("cvs").width;
    context.height = document.getElementById("cvs").height;

    document.addEventListener("keydown", captureAppuiToucheClavier)
    document.addEventListener("keyup", captureRelacheToucheClavier)

    boucleDeJeu();
}


let Datenew = Date.now()
function boucleDeJeu() {

    if (plateau.estStart){
        update(Date.now()-Datenew);  
    }

    render();
    
    requestAnimationFrame(boucleDeJeu);
    Datenew = Date.now()
}

function update(dt) {
    
    plateau.verifSiVictoir()

    if (plateau.victoire == false){
        plateau.colision(time.milSec)
        plateau.vehicules[0].update(dt)
        time.update(dt)
    }
    
}

function render() {

    context.clearRect(0, 0, context.width, context.height);

    if (plateau.estStart && plateau.victoire == false){

        plateau.render(context,plateau.vehicules[0].position.x-960,plateau.vehicules[0].position.y-540);

        plateau.renderObstacle(context,plateau.vehicules[0].position.x-960,plateau.vehicules[0].position.y-540)

        for(let i=0;i<plateau.vehicules.length;i++){
            plateau.vehicules[i].render(context);
        }
        
    } else if (plateau.victoire == false){
        context.drawImage(start,0,0,1920,1080)

    } else {
        context.drawImage(victori,0,0,1920,1080)
        time.render(context)
    }

    time.render(context)
        plateau.vehicules[0].timer1.render(context)
        plateau.vehicules[0].timer2.render(context)
        plateau.vehicules[0].timer3.render(context)
    
}

function captureAppuiToucheClavier(event) {
    // pratique pour connaître les keyCode des touches du clavier :
    //  --> http://www.cambiaresearch.com/articles/15/javascript-key-codes
    if (event.code == "ArrowUp"){
        plateau.vehicules[0].setSpeed(1)
    }
    if (event.code == "ArrowDown"){
        plateau.vehicules[0].setSpeed(-1)
    }
    if (event.code == "ArrowLeft"){
        plateau.vehicules[0].tourner(-1)
    }
    if (event.code == "ArrowRight"){
        plateau.vehicules[0].tourner(1)
    }
    if (event.code == "Enter" && plateau.estStart == false){
        plateau.estStart = true
    }

}

function captureRelacheToucheClavier(event) {
    if (event.code == "ArrowUp"){
        plateau.vehicules[0].setSpeed(0)
    }
    if (event.code == "ArrowDown"){
        plateau.vehicules[0].setSpeed(0)
    }
    if (event.code == "ArrowLeft"){
        plateau.vehicules[0].tourner(0)
    }
    if (event.code == "ArrowRight"){
        plateau.vehicules[0].tourner(0)
    }
}