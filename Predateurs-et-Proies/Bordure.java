public class Bordure extends Case {
    
    Bordure (Position position,Plateau plateau){
        super(position,'X', plateau);
    }
    public void afficherCase(){
        Ecran.afficher('X');
    }

    public void redirection(Case entite){
        
          
    }

}
