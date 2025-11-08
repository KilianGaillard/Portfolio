
class ObstacleRebond extends Obstacle {

    constructor(posX,posY,longueur,largeur,itBox,source){
        super(posX,posY,longueur,largeur,itBox)
        this.image = new Image;
        this.image.src = source;
    }
 
}