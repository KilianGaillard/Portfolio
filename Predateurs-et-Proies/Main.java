public class Main {

    public static void main(String[] args) {
      //création du plateau, puis paramètrages de celui-ci défini par l'utilisateur, et 
      //instaciation du nombre d'entités minimales requises pour éxécuter le jeu
        Plateau PlateauDeJeu = new Plateau();
        PlateauDeJeu.initialisationUtilisateur();
        int number_entité_min=3;
      
    //boucle de jeu principale
        //appel de la méthode interaction sur tous les personnages, pour se faire jouer le jeu
        while(PlateauDeJeu.nombreEntitesRestantes()>=number_entité_min){
            for(int i = 1; i<PlateauDeJeu.getRowNumber()-1 ; i++){
                for(int j = 1 ; j<PlateauDeJeu.getColNumber()-1 ; j++){
                    if(PlateauDeJeu.getPlateau()[i][j].getSymbole()!='X'&& PlateauDeJeu.getPlateau()[i][j].getSymbole()!=' '&&PlateauDeJeu.getPlateau()[i][j].getSymbole()!='w'){
                        PlateauDeJeu.getPlateau()[i][j].interactionFinale(PlateauDeJeu.getPlateau()[ i + PlateauDeJeu.getPlateau()[i][j].getDirection().getRowDir()][ j + PlateauDeJeu.getPlateau()[i][j].getDirection().getColDir()]);
                        

                    }

                }
            }
            //mise à false de l'attribut qui permet de savoir si un personnage a déjà joué,
            //afin d'autoriser l'éxécution du prochain tour
            for(int k = 1; k<PlateauDeJeu.getRowNumber()-1 ; k++){
                for(int l = 1 ; l<PlateauDeJeu.getColNumber()-1 ; l++){
                    if(PlateauDeJeu.getPlateau()[k][l].getSymbole()!='X'&& PlateauDeJeu.getPlateau()[k][l].getSymbole()!=' '){
                        PlateauDeJeu.getPlateau()[k][l].setAgi(false);
                    }
                }
            }
            //numéro du tour joué +1
            PlateauDeJeu.addTour();

            //mise en attente de l'éxécution du jeu, pour rendre les différents tour du jeu bien visibles,
            //et suffisamment lents pour l'oeil humain
            try {
                Thread.sleep(2000);}
                
                catch(Exception e) {
                    Ecran.afficherln("Quelque chose ne va pas, le catch a été utilisé");
                }
                finally {
                    Ecran.afficherln("==================================================");
                    Ecran.afficherln("                     Tour n°",PlateauDeJeu.getTour());
                    Ecran.afficherln("==================================================");
                }
                //affichage du plateau
            PlateauDeJeu.afficherPlateau();
        }
    }
}