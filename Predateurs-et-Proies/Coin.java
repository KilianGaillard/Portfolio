public class Coin extends Bordure {
    
    Coin(Position position,Plateau plateau){
        super(position,plateau);
    }

    public void redirection(Case entite){
        
        if(entite.getAgi()!=true){
            Direction dir = entite.getDirection();
            dir.inverseDir();
            entite.setDirection(dir);
            entite.setAgi(true);
        }
    }
}
