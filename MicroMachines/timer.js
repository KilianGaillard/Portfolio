class Timer{

    constructor(x,y,milSec){
        this.x = x;
        this.y = y;
        this.milSec=milSec;
    }

    update(dt){
        this.milSec += dt;
    }

    render(context){
        if (this.milSec != 0){
            let centiemSec = Math.floor(this.milSec/10)
            let sec = Math.floor(centiemSec/100)
            centiemSec += -sec*100
            let min = Math.floor(sec/60)
            sec += -min*60

            context.save()
            context.font = "50px Arial "
            context.fillText(""+String(min).padStart(2,"0")+" : "+String(sec).padStart(2,"0")+" : "+String(centiemSec).padStart(2,"0"),this.x,this.y)
            context.restore()
        }
    }

}