class Plan {


    constructor(){
        this.image = new Image();
        this.image.src = "Circuit.png";
        this.obstacles = [];
        this.vehicules = [];
        this.longueur = this.image.height;
        this.largeur = this.image.width;
        this.estStart = false;
        this.victoire = false;
    }

    render(context,x,y){
        context.drawImage(this.image,-x,-y,this.image.width,this.image.height)
    }
    
    creerVehicule(x){
        for(let i=0;i<x;i++){
            this.vehicules[i] = new Vehicule(i)
        }
    }

    creerCheckpoint(){
        for (let i=0;i<11;i++){
            this.obstacles[i] = new Checkpoint(i)
        }
    }

    renderObstacle(context,x,y){
        for (let i=0;i<this.obstacles.length;i++){
            this.obstacles[i].render(context,x,y)
        }
    }

    colision(timer){
        for(let i=0;i<this.vehicules.length;i++){
            for(let j=0;j<this.obstacles.length;j++){
                this.obstacles[j].estEnColision(this.vehicules[i],timer)
            }
        }
    }

    verifSiVictoir(){
        for(let i=0;i<this.vehicules.length;i++){
            if (this.vehicules[i].nbtour == 3){
                this.victoire = true
            }
        }
    }

}