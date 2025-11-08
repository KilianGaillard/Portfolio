public class Plateau {

    private int rowNumber;
    private int colNumber;
    Case[][] plateau;
    private int tour;
 
    

    Plateau(int rowNumber, int colNumber){
        this.colNumber=colNumber;
        this.rowNumber=rowNumber;
        this.plateau = new Case[rowNumber][colNumber];
        this.tour=0;

    }

    Plateau(){
        this(8,8);
        this.tour=0;
    }
    public int getTour(){
        return this.tour;
    }
    public void addTour(){
        this.tour=this.tour+1;
    }
    public void setPlateau(int rowNumber,int colNumber){
        this.rowNumber=rowNumber;
        this.colNumber=colNumber;
        this.plateau = new Case[rowNumber][colNumber];
        
    }
    public void initialisationUtilisateur(){
        Ecran.afficherln("Quelle est le nombre de lignes que vous souhaitez pour votre plateau ? : ");
        int ligne = Clavier.saisirInt();
        Ecran.afficherln("Quelle est le nombre de colonnes que vous souhaitez pour votre plateau ? : ");
        int colonne = Clavier.saisirInt();
        this.setPlateau(ligne, colonne);
        this.generationPlateau();
        Ecran.afficherln("Quelle est le nombre de Poules que vous souhaitez ? : ");
        int poules = Clavier.saisirInt();
        Ecran.afficherln("Quelle est le nombre de Lapins que vous souhaitez ? : ");
        int lapins = Clavier.saisirInt();
        Ecran.afficherln("Quelle est le nombre de Renards que vous souhaitez ? : ");
        int renards = Clavier.saisirInt();
        Ecran.afficherln("Quelle est le nombre de Chasseurs que vous souhaitez ? : ");
        int chasseurs = Clavier.saisirInt();
        this.generationEntites(poules, lapins, renards, chasseurs);
        Ecran.afficherln("==================================================");
        Ecran.afficherln("                  Etat initial du Jeu");
        Ecran.afficherln("==================================================");
        this.afficherPlateau();
        
        
    }
    public Case[][] getPlateau(){
        return this.plateau;
    }

    public int getRowNumber(){
        return this.rowNumber;
    }
    public int getColNumber(){
        return this.colNumber;
    }
    public Case getCase(Position position){
        return plateau[position.getRow()][position.getCol()];

    }
    public void ajouterCase(Case cases){
        plateau[cases.getPosition().getRow()][cases.getPosition().getCol()] = cases;

    }
    public void clearPosition(Position position){
        this.plateau[position.getRow()][position.getCol()] = new Case(position,this);
    }
   
    public void retirerCase(Case cases){
        plateau[cases.getPosition().getRow()][cases.getPosition().getCol()] = null;

    }
    

    public void afficherPlateau(){
        for(int i=0;i<this.rowNumber;i++){
            
            for(int j=0;j<this.colNumber;j++){
                
                this.plateau[i][j].afficherCase();
                Ecran.afficher(' ');
            }
            Ecran.sautDeLigne();
        }
    }

    public void generationPlateau(){
        for(int i=0;i<rowNumber;i++){
            for(int j=0;j<colNumber;j++){
                Position position_temporaire = new Position(i,j);
                if(i==0||i==rowNumber-1||j==0||j==colNumber-1){
                    if(!(j==0 || j==colNumber-1)){
                        this.plateau[i][j] = new BordureHorizontale(position_temporaire, this);
                    } else if (!(i==0 || i==rowNumber-1)){
                        this.plateau[i][j] = new BordureVerticale(position_temporaire, this);
                    } else {
                        this.plateau[i][j] = new Coin(position_temporaire, this);
                    }
                }else {
                    this.plateau[i][j] = new Case(position_temporaire,this);
                }
            }
        }
    }
    

    public void generationEntites(int limite_entite_p,int limite_entite_l,int limite_entite_r,int limite_entite_c){
        
        
        int random_i;
        int random_j;
        for(int a=0;a<limite_entite_p;a++){
            do {
                random_i = 1+ (int)(Math.random()*(rowNumber-2));
                random_j = 1+ (int)(Math.random()*(colNumber-2));
                
            } while(plateau[random_i][random_j].getSymbole()!=' ');
            
                    Position position_temporaire = new Position(random_i, random_j);
                    Poule p = new Poule(position_temporaire,this);
                    
                    this.plateau[random_i][random_j]=p;
        }
        for(int b=0;b<limite_entite_l;b++){
            
            do {
                random_i = 1+ (int)(Math.random()*(rowNumber-2));
                random_j = 1+ (int)(Math.random()*(colNumber-2));
            } while(plateau[random_i][random_j].getSymbole()!=' ');
                    Position position_temporaire = new Position(random_i, random_j);
                    Lapin l = new Lapin(position_temporaire,this);
                    
                    this.plateau[random_i][random_j]=l;
        }
                
        
        for(int c=0;c<limite_entite_r;c++){
            do {
                random_i = 1+ (int)(Math.random()*(rowNumber-2));
                random_j = 1+ (int)(Math.random()*(colNumber-2));
        
            } while(plateau[random_i][random_j].getSymbole()!=' ');
                    Position position_temporaire = new Position(random_i, random_j);
                    Renard r = new Renard(position_temporaire,this);
                    
                    this.plateau[random_i][random_j]=r;
        }
        
        for(int d=0;d<limite_entite_c;d++){
            do {
                random_i = 1+ (int)(Math.random()*(rowNumber-2));
                random_j = 1+ (int)(Math.random()*(colNumber-2));
            } while(plateau[random_i][random_j].getSymbole()!=' ');
                    Position position_temporaire = new Position(random_i, random_j);
                    Chasseur c = new Chasseur(position_temporaire,this);
                    
                    this.plateau[random_i][random_j]=c;
        }
        
    }   


    public int nombreEntitesRestantes(){
        int nombre_entites = 0;
        for(int i=1;i<rowNumber-1;i++){
            for(int j=1;j<colNumber-1;j++){
                if(plateau[i][j].getSymbole()=='C' || plateau[i][j].getSymbole()=='R' || plateau[i][j].getSymbole()=='P' || plateau[i][j].getSymbole()=='L'){
                    nombre_entites=nombre_entites+1;
                }

            }
        }
        return nombre_entites;
    }

    

    

}
