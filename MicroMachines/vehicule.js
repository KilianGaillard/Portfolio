
class Vehicule {
    

    constructor(x) {
        this.image = new Image()
        if (x == 0){
            this.image.src = "Voiture1.png"
            this.position = {x:6590,y:2412}; // x:1660,y:603  pour la voiture 2
        } else {
            this.image.src = "Voiture2.png"
            this.position = {x:5630,y:1972};
        }
        this.hauteur = this.image.height;
        this.longueur = this.image.width;
        this.angle = 0; //degrée
        this.couleur = 0; //entier
        this.vitesse = 0; //décimal
        this.angularSpeed = 0;
        this.nbtour = 0; //entier
        this.checkpoint = 0;//entier
        this.itBox = [{x:10,y:0,r:50}]
        this.timer1 = new Timer(50,180,0)
        this.timer2 = new Timer(50,270,0)
        this.timer3 = new Timer(50,360,0)
    }

    update(dt){

        this.angle += this.angularSpeed * dt;
        this.position.x += Math.cos(this.angle * Math.PI/180) * this.vitesse * dt
        this.position.y += Math.sin(this.angle * Math.PI/180) * this.vitesse * dt
        // for (let i=0;i<this.itBox.length;i++){
        //     this.itBox[i].x += Math.cos(this.angle * Math.PI/180) * this.vitesse * dt
        //     this.itBox[i].y += Math.sin(this.angle * Math.PI/180) * this.vitesse * dt
        // }


    }

    render(context){

        context.save();
        context.translate(960,540);
        context.rotate(this.angle * Math.PI/180);
        context.drawImage(this.image,-this.longueur/2,-this.hauteur/2,this.longueur,this.hauteur);
        context.restore();
    }

    tourner(a){
        this.angularSpeed = a * 0.15
    }

    setSpeed(a){
        this.vitesse = a * 0.65
    }

    nouveauTour(timer){
        this.checkpoint = 0
        this.nbtour += 1
        if (this.nbtour == 1){
            this.timer1.milSec = timer
        } 
        if (this.nbtour == 2){
            this.timer2.milSec = timer - this.timer1.milSec
        }
        if (this.nbtour == 3){
            this.timer3.milSec = timer - this.timer2.milSec - this.timer1.milSec
        }
    }

}





