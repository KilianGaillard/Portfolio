#include "Automaton.h"
#include <iostream>
#include <queue>
#include <cassert>

namespace fa {

  Automaton::Automaton() {
  }

  bool Automaton::isValid() const {
    if(alphabet.symbols.empty()) {
      return false;
    }
    else if(SetState.states.empty()) {
      return false;
    }
    else {
      return true;
    }
  }

  bool Automaton::addSymbol(char symbol) {
    if(alphabet.symbols.count(symbol)==0){
      if(symbol==fa::Epsilon){
        return false;
      }
      if(std::isgraph(symbol)) {
         alphabet.symbols.insert(symbol);
        return true;
      }
      return false;
     
    }
    else {
      return false;
    }

  }

  bool Automaton::removeSymbol(char symbol) {
    return alphabet.symbols.erase(symbol)==1;
  }


  bool Automaton::hasSymbol(char symbol) const {
      if(alphabet.symbols.count(symbol)>0){
        return true;
      }
    return false;

  }

   std::size_t Automaton::countSymbols() const {
    return alphabet.symbols.size();

   }



  bool Automaton::addState(int state) {
    State s_from_add;

     if(SetState.states.count(state)==0){
      SetState.states.insert(std::pair<int,State>(state,s_from_add));
      return true;
    }
    else {
      return false;
    }

  }

  bool Automaton::removeState(int state) {
    for(auto it = setTransition.theSet.begin() ; it!= setTransition.theSet.end() ; ) {
      if(it->from == state || it->to == state) {
        it = setTransition.theSet.erase(it);
      }
      else {
        it++;
      }
      
    }
    return SetState.states.erase(state)==1;
  }

  bool Automaton::hasState(int state) const {

    if(SetState.states.count(state)>0) {
      return true;
    }
    return false;

  }


   std::size_t Automaton::countStates() const {
    return SetState.states.size();
   }

   void Automaton::setStateInitial(int state) {
    std::map<int,State>::iterator it;
    it = SetState.states.find(state);
    if (it != SetState.states.end()) {
      it->second.initial=true;
    }
   }

  bool Automaton::isStateInitial(int state) const {
    std::map<int,State>::const_iterator it;
    it = SetState.states.find(state);
    if (it != SetState.states.end()) {
      if(it->second.initial==true) {
        return true;
      }
    }
    return false;

  }

   
  void Automaton::setStateFinal(int state) {
    std::map<int,State>::iterator it;
    it = SetState.states.find(state);
    if (it != SetState.states.end()) {
      it->second.final=true;
    }
  }

  bool Automaton::isStateFinal(int state) const {
    std::map<int,State>::const_iterator it;
    it = SetState.states.find(state);
    if (it != SetState.states.end()) {
      if(it->second.final==true) {
        return true;
      }
    }
    return false;
  }


  bool Automaton::addTransition(int from, char alpha, int to) {
    if(hasState(from)&&(hasSymbol(alpha)||alpha==fa::Epsilon)&&hasState(to)){
      Transition trans_temp {from,alpha,to};
      return setTransition.theSet.insert(trans_temp).second;
    }
    return false;
  }


  bool Automaton::removeTransition(int from, char alpha, int to) {
    Transition trans_temp {from,alpha,to};
    auto it = setTransition.theSet.find(trans_temp);
    if (it != setTransition.theSet.end()) {
      setTransition.theSet.erase(trans_temp);
      return true;
    }
    return false;
  }

   
  bool Automaton::hasTransition(int from, char alpha, int to) const {
    Transition trans_temp {from,alpha,to};
    auto it = setTransition.theSet.find(trans_temp);
    if(it!=setTransition.theSet.end()) {
      return true;
    }
    return false;
  }

  
  std::size_t Automaton::countTransitions() const {
    return setTransition.theSet.size();
  }

  void Automaton::prettyPrint(std::ostream& os) const {

    //affichage des états initiaux et finaux

    std::set<int> init_temp;
    std::set<int> final_temp;

    for(auto it = SetState.states.begin() ; it != SetState.states.end() ;) {
      if(it->second.initial) {
        init_temp.insert(it->first);
      }
      if(it->second.final) {
        final_temp.insert(it->first);
      }
      it++;
    }
    os << "Initial states : " << "\n" << "\t";
    for(auto it2 = init_temp.begin() ; it2 != init_temp.end() ;) {
      os << *it2 << " ";
      it2++;
    }
    os <<"\n";
    os << "Final states : " << "\n" << "\t";
    for(auto it3 = final_temp.begin() ; it3 != final_temp.end() ;) {
      os << *it3 << " ";
      it3++;
    }
    os <<"\n";

    //affichage des transitions


    os << "Transitions : " << "\n";
   
    for(auto it = setTransition.theSet.begin() ; it!= setTransition.theSet.end() ; ) {
      int current_from = it->from;
      
        os << "\n";
        os << "\t For state " << current_from << " : \n";

      for(auto it2 = alphabet.symbols.begin(); it2!= alphabet.symbols.end();) {
        char current_alpha = *it2;
        os << "\n";
        os << "\t\t For letter " << current_alpha << " : ";

        auto temp_it = it;
        
        while(temp_it!= setTransition.theSet.end() && temp_it->from==current_from) {
          if(temp_it->alpha==current_alpha) {
             os << " " << temp_it->to << " ";
            
          }
         
          temp_it++;
        }
        it2++;
        
      }
      while (it != setTransition.theSet.end() && it->from == current_from) {
        ++it;
      }
      
    }
    os << "\n";
  }

  bool Automaton::hasEpsilonTransition() const {
    assert(isValid());
    for(auto it = setTransition.theSet.begin() ; it!= setTransition.theSet.end() ; ) {
      if(it->alpha==fa::Epsilon) {
        return true;
      }
      
      it++;
    }
    return false;
  }

  bool Automaton::isDeterministic() const {
    assert(isValid());
    for(auto it = setTransition.theSet.begin() ; it!= setTransition.theSet.end() ; ) {
      auto temp_it = it;
      int current_from = it->from;
      char current_alpha = it->alpha;
      int current_to = it->to;
      while(temp_it != setTransition.theSet.end()) {
        if(current_from==temp_it->from && current_to==temp_it->to && current_alpha!=temp_it->alpha) {
          return false;
        }
        if(current_from==temp_it->from && current_alpha==temp_it->alpha && current_to!=temp_it->to) {
          return false;
        }
        temp_it++;
      }
      it++;
    }
    int count_initial_states = 0;
    int count_final_states = 0;
   
    for(auto it = SetState.states.begin() ; it!= SetState.states.end() ; ) {
      if(it->second.initial) {
        count_initial_states++;
      }
      if(it->second.final) {
        count_final_states++;
      }
      it++;
    }
    if(count_initial_states==1 && count_final_states>=0) {
      return true;
    }
    return false;
  }

  bool Automaton::isComplete() const {
    assert(isValid());
    for(auto it = SetState.states.begin();it != SetState.states.end();) {
      for(auto it2 = alphabet.symbols.begin();it2 != alphabet.symbols.end();) {
        bool found = false;
         for(auto it3 = setTransition.theSet.begin() ; it3!= setTransition.theSet.end() ; ) {
            if(it3->from==it->first&& it3->alpha==*it2) {
              found = true;
              break;
            } 
            it3++;
          }
          if(!found) {
            return false;
          }
         it2++;
      }
      it++;
    }
    return true;
  }


  Automaton Automaton::createComplete(const Automaton& automaton) {

    assert(automaton.isValid());

    if(!automaton.isComplete()) {
      fa::Automaton new_automaton = automaton;

      int index_sink = 0;
       //on crée un état poubelle
      for(size_t a = 0;a<automaton.countStates()+1;a++) {
        if(!automaton.hasState(a)) {
          new_automaton.addState(a);
          index_sink = a;
          break;
        }
      }
      //on lie les états qui ne couvrent pas tous les symboles avec l'état poubelle

      for(auto it2 = new_automaton.SetState.states.begin();it2 != new_automaton.SetState.states.end();) {
        for(auto it3 = new_automaton.alphabet.symbols.begin();it3 != new_automaton.alphabet.symbols.end();) {
          bool found = false;
          for(auto it4 = new_automaton.setTransition.theSet.begin() ; it4!= new_automaton.setTransition.theSet.end() ; ) {
              if(it4->from==it2->first&& it4->alpha==*it3) {
                found = true;
                break;
              } 
              it4++;
            }
            if(!found) {
              new_automaton.addTransition(it2->first,*it3,index_sink);
            }
          it3++;
        }
        it2++;
      }
      assert(new_automaton.isValid());
      return new_automaton;
    }
    
    return automaton;
  }



  Automaton Automaton::createComplement(const Automaton& automaton) {
    
    assert(automaton.isValid());

      fa::Automaton new_automaton;
      for(auto it = automaton.alphabet.symbols.begin();it != automaton.alphabet.symbols.end();it++) {
        new_automaton.addSymbol(*it);
      }
      for(auto it = automaton.SetState.states.begin();it != automaton.SetState.states.end();it++) {
        new_automaton.addState(it->first);
        if(it->second.initial) {
          new_automaton.setStateInitial(it->first);
        }
      }
      for(auto it = automaton.setTransition.theSet.begin();it != automaton.setTransition.theSet.end();it++) {
        new_automaton.addTransition(it->from,it->alpha,it->to);
      }
      // if(!new_automaton.isDeterministic()) {
      //   new_automaton = createDeterministic(new_automaton);
      // }
      if(!new_automaton.isComplete()) {
        new_automaton = fa::Automaton::createComplete(new_automaton);
      }
      for(auto it = automaton.SetState.states.begin();it != automaton.SetState.states.end();it++) {
        if(!it->second.final) {
          new_automaton.setStateFinal(it->first);
        }
      }
      assert(new_automaton.isValid());
      return new_automaton;
  }



  Automaton Automaton::createMirror(const Automaton& automaton) {

    assert(automaton.isValid());

      fa::Automaton new_automaton;
      for(auto it = automaton.alphabet.symbols.begin();it != automaton.alphabet.symbols.end();it++) {
        new_automaton.addSymbol(*it);
      }
      for(auto it2 = automaton.SetState.states.begin();it2 != automaton.SetState.states.end();it2++) {
        new_automaton.addState(it2->first);
        if(it2->second.initial) {
          new_automaton.setStateFinal(it2->first);
        }
        if(it2->second.final) {
          new_automaton.setStateInitial(it2->first);
        }
      }
      for(auto it3 = automaton.setTransition.theSet.begin();it3 != automaton.setTransition.theSet.end();it3++) {
        new_automaton.addTransition(it3->to,it3->alpha,it3->from);
      }
      
      assert(new_automaton.isValid());
      return new_automaton;


  }


  std::set<int> Automaton::makeTransition(const std::set<int>& origin, char alpha) const {

    assert(isValid());
    std::set<int> result;
     for(auto it = origin.begin();it != origin.end();it++) {
      for(auto it2 = setTransition.theSet.begin();it2 != setTransition.theSet.end();it2++) {
        if(it2->from==*it && alpha == it2->alpha) {
          result.insert(it2->to);
        }
      }
     }
     return result;
  }

  std::set<int> Automaton::readString(const std::string& word) const {
    std::set<int> result;
    for(auto it = SetState.states.begin(); it != SetState.states.end();it++) {
      if(it->second.initial) {
        result.insert(it->first);
      }
    }
    for(char letter : word) {
      result = makeTransition(result,letter);
    }
    return result;
  }

  bool Automaton::match(const std::string& word) const {
    assert(isValid());
    std::set<int> result = readString(word);
    for(auto it = SetState.states.begin(); it != SetState.states.end();it++) {
      if(result.find(it->first) != result.end()) {
        if(isStateFinal(it->first)) {
          return true;
        }
      }
    }
    return false;
  }


  bool Automaton::isLanguageEmpty() const {
    assert(isValid());
    std::queue<int> to_visit;
    std::set<int> already_visited;
    for(auto it = SetState.states.begin(); it != SetState.states.end();it++) {
      if(it->second.initial) {
        to_visit.push(it->first);
        already_visited.insert(it->first);
      }
    }
    while(!to_visit.empty()) {

      int state_in_visit = to_visit.front();
      to_visit.pop();
      if(isStateFinal(state_in_visit)) {
            return false;
      }
      for(char letter : alphabet.symbols) {
        std::set<int> result = makeTransition({state_in_visit},letter);
        for(int state : result) {
          if(already_visited.find(state) == already_visited.end()) {
            already_visited.insert(state);
            to_visit.push(state);
          }
        }
      }
    }
    return true;
  }



  void Automaton::removeNonAccessibleStates() {
    assert(isValid());
    std::queue<int> to_visit;
    std::set<int> already_visited;
    for(auto it = SetState.states.begin(); it != SetState.states.end();it++) {
      if(it->second.initial) {
        to_visit.push(it->first);
        already_visited.insert(it->first);
      }
    }
    while(!to_visit.empty()) {

      int state_in_visit = to_visit.front();
      to_visit.pop();
      for(char letter : alphabet.symbols) {
        std::set<int> result = makeTransition({state_in_visit},letter);
        for(int state : result) {
          if(already_visited.find(state) == already_visited.end()) {
            already_visited.insert(state);
            to_visit.push(state);
          }
        }
      }
    }
    std::set<int> copy_states;
    for(auto it = SetState.states.begin(); it != SetState.states.end();it++) {
      copy_states.insert(it->first);
    }
    for(int state : copy_states) {
       if(already_visited.find(state)==already_visited.end()) {
        removeState(state);
      }
    }
    
  }

  void Automaton::removeNonCoAccessibleStates() {
    assert(isValid());
    fa::Automaton new_automaton = createMirror(*this);
    new_automaton.removeNonAccessibleStates();
    for(auto it = SetState.states.begin(); it!= SetState.states.end();it++) {
      if(new_automaton.SetState.states.find(it->first)==new_automaton.SetState.states.end()) {
        removeState(it->first);
      }
    }
  }
//les deux lhs et rhs ne sont pas sur le même alphabet

  Automaton Automaton::createIntersection(const Automaton& lhs, const Automaton& rhs) {

    fa::Automaton copy_lhs = lhs;
    fa::Automaton copy_rhs = rhs;
    fa::Automaton new_automaton;

    std::map<std::pair<int,int>,int> t_correspondance;
    std::queue<std::pair<int,int>> to_process;
    int count = 0;

    for(auto it = copy_lhs.SetState.states.begin(); it!= copy_lhs.SetState.states.end();it++) {
      if(copy_lhs.isStateInitial(it->first)){
        for(auto it2 = copy_rhs.SetState.states.begin(); it2!= copy_rhs.SetState.states.end();it2++) {
          if(copy_rhs.isStateInitial(it2->first)){
            std::cout << "Ca marche";
            std::pair<int,int> t_landr = {it->first,it2->first};
            to_process.push(t_landr);
            t_correspondance.insert({t_landr,count});
            new_automaton.addState(count);
            new_automaton.setStateInitial(count);
            if(new_automaton.isStateInitial(count)){
              std::cout << "est initial";
            }
            count++;
            std::cout << "Ca marche encore ?";
            
          }
        }
      }
    }
    std::set<char> alphabet_merge;
    for(auto it = copy_lhs.alphabet.symbols.begin(); it!= copy_lhs.alphabet.symbols.end();it++) {
      alphabet_merge.insert(*it);
    }
    for(auto it = copy_rhs.alphabet.symbols.begin(); it!= copy_rhs.alphabet.symbols.end();it++) {
      alphabet_merge.insert(*it);
    }

    for(char letter : alphabet_merge) {
      new_automaton.addSymbol(letter);
    }
      
    
      while(!to_process.empty()) {
        std::pair<int,int> now = to_process.front();
        to_process.pop();
        int current = t_correspondance.at(now);
        for(char letter : new_automaton.alphabet.symbols) {
          
          std::set<int> left_found = copy_lhs.makeTransition({now.first},letter);
          std::set<int> right_found = copy_rhs.makeTransition({now.second},letter);
          for(auto it3 = left_found.begin();it3!=left_found.end();it3++) {
            for(auto it4 = right_found.begin();it4!=right_found.end();it4++) {
              std::pair<int,int>to_add = {*it3,*it4};
              if (!t_correspondance.count(to_add)) {
                    t_correspondance[to_add] = count;
                    new_automaton.addState(count);
                    to_process.push(to_add);
                    count++;
              }
              new_automaton.addTransition(current,letter,t_correspondance.at(to_add));
              if(copy_lhs.isStateFinal(*it3)&&copy_rhs.isStateFinal(*it4)){
                new_automaton.setStateFinal(t_correspondance.at(to_add));
              }
            }
          }
        }
      }

      
    return new_automaton;
  }

  bool Automaton::hasEmptyIntersectionWith(const Automaton & other) const {
    return fa::Automaton::createIntersection(*this,other).isLanguageEmpty();
  }

  Automaton Automaton::createDeterministic(const Automaton& automaton) {
    return automaton;
  }

  bool Automaton::isIncludedIn(const Automaton& other) const{
    return true;
  }

  Automaton Automaton::createMinimalMoore(const Automaton& automaton) {
    return automaton;
  }

  Automaton Automaton::createMinimalBrzozowski(const Automaton& automaton) {
    return automaton;
  }
}
