public class Direction {
    
    private int rowDir;
    private int colDir;

    Direction (int rowDir, int colDir){
        this.rowDir = rowDir;
        this.colDir = colDir;
    }

    Direction (){
        this(0, 0);
    }
    public int getRowDir(){
        return this.rowDir;
    }

    public int getColDir(){
        return this.colDir;
    }
    public void setDirection(int rowDir,int colDir){
            this.rowDir=rowDir;
            this.colDir=colDir;
    }


    public void generationDirection(){
        double random_row = Math.random();
        double random_col = Math.random();
        int colDir=0;
        int rowDir=0;
        while(rowDir==0&&colDir==0){
            if(random_row >=0 && random_row<0.33){
                rowDir=-1;

            }
            if(random_row >=0.33 && random_row<0.66){
                rowDir=0;

            }
            if(random_row >=0.66 && random_row<=1){
                rowDir=1;

            }
            if(random_col >=0 && random_col<0.33){
                colDir=-1;

            }
            if(random_col >=0.33 && random_col<0.66){
                colDir=0;

            }
            if(random_col >=0.66 && random_col<=1){
                colDir=1;

            }
        }
        this.setDirection(rowDir,colDir);
    }

    public void inverseCol(){
        this.colDir = -this.colDir;
    }

    public void inverseRow(){
        this.rowDir = -this.rowDir;
    }

    public void inverseDir(){
        this.inverseCol();
        this.inverseRow();
    }

    

    
}
