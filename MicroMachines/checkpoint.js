class Checkpoint extends Obstacle {

    constructor(n){
        switch (n) {
            case 0:
                super(7400,2500,500,500,[{x:7400,y:2500,r:500}])
                break;
            case 1:
                super(11200,4000,500,500,[{x:11200,y:4000,r:500}])
                break;
            case 2:
                super(7600,3200,400,400,[{x:7600,y:3200,r:500}])
                break;
            case 3:
                super(8500,4800,400,400,[{x:8500,y:4800,r:500}])
                break;
            case 4:
                super(7700,6100,400,400,[{x:7700,y:6100,r:500}])
                break;
            case 5:
                super(6950,5450,400,400,[{x:6950,y:5450,r:500}])
                break;
            case 6:
                super(6150,5500,400,400,[{x:6150,y:5500,r:500}])
                break;
            case 7:
                super(6970,4250,500,500,[{x:6970,y:4250,r:500}])
                break;
            case 8:
                super(5200,4700,500,500,[{x:5200,y:4700,r:500}])
                break;
            case 9:
                super(5250,3400,500,500,[{x:5250,y:3400,r:500}])
                break;
            case 10:
                super(4100,3000,500,500,[{x:4100,y:3000,r:500}])
                break;
        
            default:
                super(0,0,0,0,null)
                break;
        }
        this.num = n
        
    }

    actionColision(vehicule,timer){
        if(vehicule.checkpoint == this.num-1){
            vehicule.checkpoint = this.num
        } else if(vehicule.checkpoint == 10 && this.num == 0){
            vehicule.nouveauTour(timer)
        }
    }

    estEnColision(vehicule,timer){
        for (let i=0;i<this.itBox.length;i++){
            for (let j=0;j<vehicule.itBox.length;j++){
                let dx = this.itBox[i].x - (vehicule.itBox[j].x + vehicule.position.x)
                let dy = this.itBox[i].y - (vehicule.itBox[j].y + vehicule.position.y)
                let distance = Math.sqrt(dx * dx + dy * dy)
                if (distance < this.itBox[i].r + vehicule.itBox[j].r){
                    this.actionColision(vehicule,timer)
                }
            }
        }
    }
}