public class Renard extends Predateurs {
  
    Renard (Position position,Plateau plateau){
        super(position, 'R',plateau);
    }
    public void afficherCase(){
        Ecran.afficher('R');
    }
    public void interactionFinale(Case entite){
        if(!this.getAgi()){
            if(entite.getSymbole()!=' '&&entite.getSymbole()!='X'){
                if(entite.getSymbole()=='C'||entite.getSymbole()=='R'){
                    this.getDirection().generationDirection();
                    this.setAgi(true);
                }else{
                    //l'entité(la case) est supprimée de la matrice plateau
                    this.tuerEntite(entite);
                    this.setAgi(true);
                }
            }
            
            else{
                entite.redirection(this);
            }
        }
    }
    
}
