public class Case {
    private Direction direction;
    private Position position ;
    private char symbole;
    private Plateau plateau;
    private boolean a_deja_agi = false;
    


    Case (Position position,char symbole, Plateau plateau) {
        this.position = position;
        this.symbole = symbole;
        this.direction = new Direction();
        this.plateau=plateau;
        
    }

    Case (Position position,Plateau plateau){
        this.position=position;
        this.plateau=plateau;
        this.symbole=' ';
        
    }

    public Direction getDirection(){
        return this.direction;
    }
    public Plateau getPlateauType(){
        return this.plateau;
    }
    public boolean getAgi(){
        return this.a_deja_agi;
    }
    public void setAgi(boolean bool){
        this.a_deja_agi=bool;
    }


    public void setDirection(Direction direction){
        this.direction=direction;
    }
    public void interactionFinale(Case entite){


    }
    public void redirection(Case entite){
        
        if(!entite.getAgi()){
            Position position_cible = new Position(this.getPosition().getRow(),this.getPosition().getCol());
            Case case_cible = new Case(position_cible,this.getPlateauType());
            this.getPlateauType().clearPosition(entite.getPosition());
            entite.getPlateauType().retirerCase(case_cible);
            entite.setPosition(case_cible.getPosition());
            entite.getPlateauType().ajouterCase(entite);
            entite.setAgi(true);
        }
    }
   


    public void setCase(Position position,char symbole){
        this.position=position;
        this.symbole=symbole;
    }

    public void setPosition(Position position){
        this.position=position;
        
    }

    public Position getPosition(){
        return this.position;
    }

    public void tuerEntite(Case entite){
        this.getPlateauType().clearPosition(entite.getPosition());

    }

    
    public char getSymbole(){
        
        return this.symbole;
    }

    public void afficherCase(){
        Ecran.afficher(this.getSymbole());
        
    }
    
    public void afficherDirection(){
        Ecran.afficherln("(",this.getDirection().getRowDir()," ," ,this.getDirection().getColDir(),")");
    }
  
}
    




