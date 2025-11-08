public class Personnage extends Case {

    Personnage (Position position ,char symbole,Plateau plateau){
        super(position, symbole,plateau);
        this.getDirection().generationDirection();

    }
    
    public void interactionFinale(Case entite){

    }

   

}
