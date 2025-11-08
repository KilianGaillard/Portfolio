import java.util.*;
import java.io.*;
import java.lang.reflect.Field;

/**
 * Fonctions d'affichage dans la console d'exécution.
 *
 */
 
public class Ecran {

    /**
     * Affichage dans la console d'exécution.<p>
     * Indication entre parenthèses de la liste des 0, 1 ou plusieurs parametres à afficher:<br>
     * - variables,<br>
     * - constantes,<br>
     * - expressions,<br>
     * - ...
     *
     * @param s La liste des parametres a afficher.
     */
    public static void afficher(Object... s) {
	int i = 0;
	for (Object o : s) {
	    i++;
	    System.out.print(o.toString());
	}
    }

    /**
     * Affichage dans la console d'exécution suivi d'un saut de ligne.<p>
     * Indication entre parenthèses de la liste des 0, 1 ou plusieurs parametres à afficher:<br>
     * - variables,<br>
     * - constantes,<br>
     * - expressions,<br>
     * - ...
     *
     * @param s La liste des parametres a afficher.
     */
    public static void afficherln(Object... s) {
	for (Object o : s) {
	    System.out.print(o.toString());
	}
	System.out.println();
    }

    /**
     * Saut de ligne dans la console d'exécution.
     *
     */
    public static void sautDeLigne() {
	System.out.println();
    }

    /**
     * Affichage dans la console d'exécution d'une liste de paramètres individuellement formatés.<br>
     * L'affichage est spécifié au moyen d'une String qui peut contenir du texte fixe et 0, 1 ou plus descripteurs d'affichage.<br>
     * A chaque descripteur doit correspondre un paramètre compatible dans la liste des paramètres d'appel.<br>
     * Exemple:<br>
     * &nbsp;&nbsp;Ecran.formater("Resultat : %3d %6.2f",a,b);<br>
     * affiche la chaine "Resultat : ", suivie de la valeur du paramètre a convertie en entier formatée sur 3 caractères de large au minimum (cadrage à droite éventuellement complémenté par autant d'espaces que nécessaire),
     * suivie d'un espace, suivie de la valeur du paramètre b convertie en réel flottant formatée sur 6 caractères de large au minimum avec 2 chiffres après la virgule (cadrage à droite éventuellement complémenté par autant d'espaces que nécessaire).<br>
     * Deux descripteurs apparaissent dans la chaine format: %3d et %6.2f. Deux paramètres doivent être passés à la fonction en plus de la chaine format. Il s'agit de a et de b.<br>
     * <br>
     * Chaque descripteur d'affichage doit respecter la syntaxe suivante: %[index_parametre$][drapeaux][largeur][.precision]conversion.<br>
     * &nbsp;&nbsp; - index_parametre (optionnel) est un entier qui indique la position de l'argument dans la liste des paramètres. Le premier paramètre est referencé par "1$", le second par "2$", ...<br>
     * &nbsp;&nbsp; - drapeaux (optionnel) est un ensemble de charactères qui modifie l'affichage. L'ensemble des drapeaux valides dépend de conversion (voir + loin).<br>
     * &nbsp;&nbsp; - largeur (optionnel) est un entier non négatif indiquant le nombre minimum de caractères devant être affichés.<br>
     * &nbsp;&nbsp; - precision (optionnel) est un entier non négatif habituellement utilisé pour contrôler/restreindre le nombre de charactères affichés. Le comportement spécifique de precision dépend de conversion (voir + loin).<br>
     * &nbsp;&nbsp; - conversion (requis) est un charactère indiquant selon quelle conversion (vers quel type) le paramètre doit être converti et formaté. L'ensemble des conversions valides pour un paramètre donné depend du type de donnée du paramètre.<br>
     * <br>
     * La table suivante liste de manière non exhaustive les valeurs possibles de conversion, le type de paramètre sur lequel la conversion est possible, et, le résultat affiché.<br>
     * &nbsp;&nbsp; - 'b', 'B' : Tous types : Si le paramètre est boolean, l'affichage est "true" ou "false" suivant sa valeur. Dans les autres cas, l'affichage est "true".<br>
     * &nbsp;&nbsp; - 's', 'S' : Tous types : L'affichage est réalisé par conversion en une chaine de caractères.<br>
     * &nbsp;&nbsp; - 'c', 'C' : char : L'affichage du caractère est réalisé.<br> 
     * &nbsp;&nbsp; - 'd' : byte, short, int, long : L'affichage de l'entier est réalisé en valeur décimale (base 10).<br>  
     * &nbsp;&nbsp; - 'o' : byte, short, int, long : L'affichage de l'entier est réalisé en valeur octale (base 8).<br>
     * &nbsp;&nbsp; - 'x', 'X' : byte, short, int, long : L'affichage de l'entier est réalisé en valeur hexadécimale (base 16).<br>  
     * &nbsp;&nbsp; - 'e', 'E' : float, double : L'affichage du réel est réalisé sous forme mantisse exposant avec precision chiffres après la virgule sur la mantisse quitte à dépasser largeur.<br>
     * &nbsp;&nbsp; - 'f' : float, double : L'affichage du réel est réalisé sous forme partie entière.partie décimale avec precision chiffres après la virgule quitte à dépasser largeur.<br>
     * <br>
     * La table suivante liste de manière non exhaustive les valeurs possibles pour les drapeaux.<br>
     * &nbsp;&nbsp; - '-' : Tous types : L'affichage est cadré à gauche (cadrage à droite si '-' non spécifié, pas de cadrage au centre).<br>
     * &nbsp;&nbsp; - '+' : Types numériques : L'affichage inclut toujours un signe (+ pour les nombres positifs, - pour les nombres négatifs).<br>
     * &nbsp;&nbsp; - ' ' : Types numériques : L'affichage inclut toujours un espace à gauche.<br>
     * &nbsp;&nbsp; - '0' : Types numériques : L'affichage est complété par des zéros à gauche au lieu d'espaces.<br>
     * <br>
     * La valeur largeur definit le nombre de caractères utilisés au minimum pour l'affichage. Si la valeur à afficher necessite moins de caractères que spécifié, des caractères supplémentaires sont ajoutés selon le format défini. Si ce n'est pas le cas, la valeur est complètement affichée en utilisant plus de caractères que ne le spécifie largeur.<br>
     * <br>
     * La valeur précision définit le nombre de chiffres de précision utilisés pour les nombres réels.<br>
     * <br>
     * L'inclusion d'un \n dans la chaine format entraine un saut à la ligne.<br>
     * <br>
     * Exemple 1:<br>
     * <code>&nbsp;&nbsp;int a = 5;</code><br>
     * <code>&nbsp;&nbsp;double d = 3.14159;</code><br>
     * <code>&nbsp;&nbsp;boolean b = true;</code><br>
     * <code>&nbsp;&nbsp;char c = 'a';</code><br>
     * <code>&nbsp;&nbsp;String s = "Test";</code><br>
     * <code>&nbsp;&nbsp;Ecran.formater("%8d\n%8.2f\n%8b\n%8c\n%8s\n",a,d,b,c,s);</code><br>
     * conduit aux affichages suivants où les items affichés sont cadrés à droite sur 8 caractères de large et le nombre réel est affiché avec deux chiffres après la virgule:<br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5</code><br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;3.14</code><br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;true</code><br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;a</code><br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;Test</code><br>
     * <br>
     * Exemple 2:<br>
     * <code>&nbsp;&nbsp;int i = 1005;</code><br>
     * <code>&nbsp;&nbsp;Ecran.formater("%8d\n%8o\n%8x\n%8b\n%8s\n%8c\n",i,i,i,i,i,i);</code><br>
     * conduit aux affichages suivants où le nombre entier 1005 est affiché en base 10, en base 8 (octal), en base 16 (hexadécimal), après conversion en booléen, après conversion en chaine de caractères, après conversion en caractère (? car pas de caractère code ASCII 1005):<br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;1005</code><br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;1755</code><br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3ed</code><br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;true</code><br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;1005</code><br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;?</code><br>
     * <br>
     * Exemple 3:<br>
     * <code>&nbsp;&nbsp;double d = 367.1415934574267;</code><br>
     * <code>&nbsp;&nbsp;Ecran.formater("%8e\n%8f\n",d,d);</code><br>
     * <code>&nbsp;&nbsp;Ecran.formater("%8.3e\n%8.3f\n",d,d);</code><br>
     * <code>&nbsp;&nbsp;Ecran.formater("%20.12e\n%20.12f\n",d,d);</code><br>
     * <code>&nbsp;&nbsp;Ecran.formater("%8b\n%8s\n",d,d);</code><br>
     * conduit aux affichages suivants où un nombre réel est affiché en notation partie entière.partie décimale ou en notation mantisse exposant selon différents scénarii de largeur et de précision, et, après conversion en booléen et en chaine de caractères:<br>
     * <code>3.671416e+02</code><br>
     * <code>367.141593</code><br>
     * <code>3.671e+02</code><br>
     * <code>&nbsp;367.142</code><br>
     * <code>&nbsp;&nbsp;3.671415934574e+02</code><br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;367.141593457427</code><br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;true</code><br>
     * <code>367.1415934574267</code><br>
     * <br>
     * Exemple 4:<br>
     * <code>&nbsp;&nbsp;int i = 1005;</code><br>
     * <code>&nbsp;&nbsp;Ecran.formater("%-8d\n%+8d\n% 8d\n%08d\n",i,i,i,i);</code><br>
     * <code>&nbsp;&nbsp;Ecran.formater("%+08d\n",i);</code><br>
     * <code>&nbsp;&nbsp;int j = -1005;</code><br>
     * <code>&nbsp;&nbsp;Ecran.formater("%-8d\n%+8d\n% 8d\n%08d\n",j,j,j,j);</code><br>
     * <code>&nbsp;&nbsp;Ecran.formater("%+08d\n",j);</code><br>
     * conduit aux affichages suivants où les drapeaux '-', '+', ' ' et '0' montre le cadrage à gauche, l'ajout obligatoire du signe + pour les nombres positifs, l'ajout d'un espace à gauche et l'utilisation de '0' au lieu de ' ' pour la complémentation à gauche de façon à afficher le bon nombre de caractères pour atteindre la largeur spécifiée :<br>
     * <code>1005&nbsp;&nbsp;&nbsp;&nbsp;</code><br>
     * <code>&nbsp;&nbsp;&nbsp;+1005</code><br>
     * <code>&nbsp;&nbsp;&nbsp;&nbsp;1005</code><br>
     * <code>00001005</code><br>
     * <code>+0001005</code><br>
     * <code>-1005&nbsp;&nbsp;&nbsp;</code><br>
     * <code>&nbsp;&nbsp;&nbsp;-1005</code><br>
     * <code>&nbsp;&nbsp;&nbsp;-1005</code><br>
     * <code>-0001005</code><br>
     * <code>-0001005</code><br>
     *
     * @param format La chaine de caratères décrivant le format d'affichage.
     * @param s La liste des paramètres à afficher.
     */  
    public static void formater(String format,Object... s) {
	ByteArrayOutputStream baos = new ByteArrayOutputStream();
	PrintStream ps = new PrintStream(baos);
	ps.printf(Locale.US,format,s);
	System.out.print(baos);
    }

    private static boolean estPredefini(Class c) {
	String s = c.getName();
	return(s.equals("java.lang.Byte") ||
	       s.equals("java.lang.Short") ||
	       s.equals("java.lang.Float") ||
	       s.equals("java.lang.Double") ||
	       s.equals("java.lang.Boolean") ||
	       s.equals("java.lang.String") ||
	       s.equals("java.lang.Integer") ||
	       s.equals("java.lang.Long") ||
	       s.equals("java.lang.Character"));
    }
  
    private static void afficherTableau(Object o) {
	if ( o.getClass().getComponentType().toString().equals("int") ) {
	    int [] to =(int []) o;
	    System.out.print("(");
	    for ( int i = 0 ; i < to.length ; i++ ) {
		if ( i != 0 )
		    System.out.print(",");
		System.out.print(to[i]); }
	    return; }
	if ( o.getClass().getComponentType().toString().equals("double") ) {
	    double [] to =(double []) o;
	    System.out.print("(");
	    for ( int i = 0 ; i < to.length ; i++ ) {
		if ( i != 0 )
		    System.out.print(",");
		System.out.print(to[i]); }
	    return; }
	if ( o.getClass().getComponentType().toString().equals("long") ) {
	    long [] to =(long []) o;
	    System.out.print("(");
	    for ( int i = 0 ; i < to.length ; i++ ) {
		if ( i != 0 )
		    System.out.print(",");
		System.out.print(to[i]); }
	    return; }
	if ( o.getClass().getComponentType().toString().equals("short") ) {
	    short [] to =(short []) o;
	    System.out.print("(");
	    for ( int i = 0 ; i < to.length ; i++ ) {
		if ( i != 0 )
		    System.out.print(",");
		System.out.print(to[i]); }
	    return; }
	if ( o.getClass().getComponentType().toString().equals("float") ) {
	    float [] to =(float []) o;
	    System.out.print("(");
	    for ( int i = 0 ; i < to.length ; i++ ) {
		if ( i != 0 )
		    System.out.print(",");
		System.out.print(to[i]); }
	    return; }
	if ( o.getClass().getComponentType().toString().equals("boolean") ) {
	    boolean [] to =(boolean []) o;
	    System.out.print("(");
	    for ( int i = 0 ; i < to.length ; i++ ) {
		if ( i != 0 )
		    System.out.print(",");
		System.out.print(to[i]); }
	    return; }
	if ( o.getClass().getComponentType().toString().equals("byte") ) {
	    byte [] to =(byte []) o;
	    System.out.print("(");
	    for ( int i = 0 ; i < to.length ; i++ ) {
		if ( i != 0 )
		    System.out.print(",");
		System.out.print(to[i]); }
	    return; }
	if ( o.getClass().getComponentType().toString().equals("char") ) {
	    char [] to =(char []) o;
	    System.out.print("(");
	    for ( int i = 0 ; i < to.length ; i++ ) {
		if ( i != 0 )
		    System.out.print(",");
		System.out.print(to[i]); }
	    return; }
	if ( o.getClass().getComponentType().toString().equals("String") ) {
	    String [] to =(String []) o;
	    System.out.print("(");
	    for ( int i = 0 ; i < to.length ; i++ ) {
		if ( i != 0 )
		    System.out.print(",");
		System.out.print(to[i]); }
	    return; }
    }
  
    private static void afficherType2(Object o) {
	if ( o == null ) {
	    System.out.print("null");
	    return; }
	Class c = o.getClass();
	if ( c.isArray() ) {
	    if ( c.getComponentType().isPrimitive() ) {
		afficherTableau(o); }
	    else {
		Object [] to =(Object []) o;
		System.out.print("(");
		for ( int i = 0 ; i < to.length ; i++ ) {
		    if ( i != 0 )
			System.out.print(",");
		    afficherType2(to[i]); } }
	    System.out.print(")"); }
	else {
	    if ( estPredefini(c) ) {
		System.out.print(o); }
	    else {
		Field [] flds = c.getDeclaredFields();
		System.out.print("(");
		for ( int i = 0 ; i < flds.length ; i++ ) {
		    if ( i != 0 )
			System.out.print(",");
		    try {
			afficherType2(flds[i].get(o)); }
		    catch(IllegalAccessException iae) { } }
		System.out.print(")"); } }
    }
  
    /**
     * Affichage dans la console d'exécution.<p>
     * Indication entre parenthèses de la liste des 0, 1 ou plusieurs parametres à afficher:<br>
     * - variables,<br>
     * - constantes,<br>
     * - expressions,<br>
     * - ...
     *
     * @param crlf Retour à la ligne ou non en fin d'affichage.
     * @param s La liste des parametres à afficher.
     */
    private static void afficherType(boolean crlf,Object... s) {
	for (Object o : s) {
	    afficherType2(o); }
	if ( crlf )
	    System.out.println();
    }
  
    /**
     * Affichage dans la console d'exécution.<p>
     * Indication entre parenthèses de la liste des 0, 1 ou plusieurs parametres à afficher:<br>
     * - variables,<br>
     * - constantes,<br>
     * - expressions,<br>
     * - ...
     *
     * @param s La liste des parametres à afficher.
     */
    private static void afficherType(Object... s) {
	afficherType(false,s);
    }
}
