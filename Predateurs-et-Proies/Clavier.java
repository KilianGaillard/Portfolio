import java.io.InputStreamReader;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.Console;

/**
 * Fonctions de saisie clavier.
 *
 */

public class Clavier {
  
    private static BufferedReader flux = new BufferedReader(new InputStreamReader(System.in));
    private Clavier(){}
    
    /**
     * Saisie au clavier d'une valeur de type byte (octet).
     * Vérification de l'information saisie et nouvelle sollicitation tant qu'elle ne correspond pas à une valeur de type byte.
     *
     * @return Le byte saisi au clavier.
     */
    public static byte saisirByte(){
	byte b = 0;
	boolean ko = true;
	while (ko) {
	    try{
		b = Byte.valueOf(flux.readLine()).byteValue();
		ko = false;
	    } catch(Exception e){
		System.err.println("Erreur : la valeur saisie n'est pas un byte. Recommencez.");
	    }
	}
	return b;
    }
    
    /**
     * Saisie au clavier d'une valeur de type short (entier sur 2 octets).
     * Vérification de l'information saisie et nouvelle sollicitation tant qu'elle ne correspond pas à une valeur de type short.
     *
     * @return Le short saisi au clavier.
     */
    public static short saisirShort(){
	short s = 0;
	boolean ko = true;
	while (ko) {
	    try{
		s = Short.valueOf(flux.readLine()).shortValue();	
		ko = false;
	    } catch(Exception e){
		System.err.println("Erreur : la valeur saisie n'est pas un short. Recommencez."); 
	    }
	}
	return s;
    }
    
    /**
     * Saisie au clavier d'une valeur de type int (entier sur 4 octets).
     * Vérification de l'information saisie et nouvelle sollicitation tant qu'elle ne correspond pas à une valeur de type int.
     *
     * @return Le int saisi au clavier.
     */
    public static int saisirInt(){
	int i = 0;
	boolean ko = true;
	while (ko) {
	    try{
		i = Integer.valueOf(flux.readLine()).intValue();	
		ko = false;
	    } catch(Exception e){
		System.err.println("Erreur : la valeur saisie n'est pas un int. Recommencez.");
	    }
	}
	return i;
    }
    
    /**
     * Saisie au clavier d'une valeur de type long (entier sur 8 octets).
     * Vérification de l'information saisie et nouvelle sollicitation tant qu'elle ne correspond pas à une valeur de type long.
     *
     * @return Le long saisi au clavier.
     */
    public static long saisirLong(){
	long l = 0;
	boolean ko = true;
	while (ko) {
	    try{
		l = Long.valueOf(flux.readLine()).longValue();
		ko = false;
	    } catch(Exception e){
		System.err.println("Erreur : la valeur saisie n'est pas un long. Recommencez.");	
	    }
	}
	return l;
    }
    
    /**
     * Saisie au clavier d'une valeur de type double (réel double précision (sur 8 octets)).
     * Vérification de l'information saisie et nouvelle sollicitation tant qu'elle ne correspond pas à une valeur de type double.
     *
     * @return Le double saisi au clavier.
     */
    public static double saisirDouble(){
	double d = 0;
	boolean ko = true;
	while (ko) {
	    try{
		d = Double.valueOf(flux.readLine()).doubleValue();
		ko = false;
	    } catch(Exception e){
		System.err.println("Erreur : la valeur saisie n'est pas un double. Recommencez.");
	    }
	}
	return d;
    }
    
    /**
     * Saisie au clavier d'une valeur de type float (réel simple précision (sur 4 octets)).
     * Vérification de l'information saisie et nouvelle sollicitation tant qu'elle ne correspond pas à une valeur de type float.
     *
     * @return Le float saisi au clavier.
     */
    public static float saisirFloat(){
	float f = 0;
	boolean ko = true;
	while (ko) {
	    try{
		f = Float.valueOf(flux.readLine()).floatValue();
		ko = false;
	    } catch(Exception e){
		System.err.println("Erreur : la valeur saisie n'est pas un float. Recommencez.");	
	    }
	}
	return f;
    }
    
    /**
     * Saisie au clavier d'une valeur de type char (caractère).
     * Vérification de l'information saisie et nouvelle sollicitation tant qu'elle ne correspond pas à une valeur de type char.
     *
     * @return Le char saisi au clavier.
     */
    public static char saisirChar(){
	char c = ' ';
	boolean ko = true;
	while (ko) {
	    try{
		String line = flux.readLine();
		if (line.length() > 0) {
		    c = line.charAt(line.length()-1);
		    ko = false;
		}
	    } catch(Exception e){
		System.err.println("Erreur : la valeur saisie n'est pas un char. Recommencez.");
	    }
	}
	return c;
    }
    
    /**
     * Saisie au clavier d'une valeur de type chaine de caractères.
     * Verification de l'information saisie et nouvelle sollicitation tant qu'elle ne correspond pas à une valeur de type chaine de caractères.
     *
     * @return La String saisie au clavier.
     */
    public static String saisirString(){
	String s = "";
	boolean ko = true;
	while (ko) {
	    try{
		s = flux.readLine();
		ko = false;
	    } catch(Exception e){
		System.err.println("Erreur : la valeur saisie n'est pas une chaine. Recommencez.");	
	    }
	}
	return s;
    }
    
    /**
     * Attente de la frappe au clavier d'une touche.
     *
     * @return Le caractere saisi au clavier.
     */
    public static char attendreCaractere(){
	char c = 0x00;
	int v;
	try {
	    v = System.in.read();
	    Thread.sleep(1);
	    c =(char) v; }
	catch (Exception e) {
	    v = 0x00; }
	return c;
    }
}
