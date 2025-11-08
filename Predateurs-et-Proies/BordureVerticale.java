public class BordureVerticale extends Bordure {
    
    BordureVerticale(Position position,Plateau plateau){
        super(position,plateau);
    }

    public void redirection(Case entite){
        
        if(entite.getAgi()!=true){
            Direction dir = entite.getDirection();
            dir.inverseCol();
            entite.setDirection(dir);
            entite.setAgi(true);
        }
    }
}
