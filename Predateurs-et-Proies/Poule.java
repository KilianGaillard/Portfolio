public class Poule extends Proies {
    
    Poule (Position position,Plateau plateau){
        super(position, 'P',plateau);
    }
    public void afficherCase(){
        Ecran.afficher('P');
    }

    public void interactionFinale(Case entite){
        if(!this.getAgi()){
            if(entite.getSymbole()!=' '&&entite.getSymbole()!='X'){
                Direction new_direction = new Direction(-this.getDirection().getRowDir(),-this.getDirection().getColDir());
                this.setDirection(new_direction);
                this.setAgi(true);
            }
            else{
                entite.redirection(this);
            }
            
        }
    }
    
    
}
