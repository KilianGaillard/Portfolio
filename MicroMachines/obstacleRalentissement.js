class ObstacleRalentissement extends Obstacle {


    constructor(posX,posY,longueur,largeur,itBox,puissanceRalentissement,source){
        super(posX,posY,longueur,largeur,itBox);
        this.puissanceRalentissement=puissanceRalentissement;
        this.image = new Image;
        this.image.src = source;
    }

}
