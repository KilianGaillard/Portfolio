
#include "gtest/gtest.h"

#include "Automaton.h"


////////////////////////////////////// tests pour IsValid /////////////////////////////////////////////////

TEST(AutomatonTestIsValid, EmptyAutomaton) {
  fa::Automaton fa;
  EXPECT_FALSE(fa.isValid());
}

TEST(AutomatonTestIsValid, ValidAutomaton) {
  fa::Automaton fa;
  fa.addSymbol('A');
  fa.addState(0);
  EXPECT_TRUE(fa.isValid());
}

TEST(AutomatonTestIsValid, AutomatonWithoutStates) {
  fa::Automaton fa;
  fa.addSymbol('A');
  EXPECT_FALSE(fa.isValid());
}

TEST(AutomatonTestIsValid, AutomatonWithoutSymbols) {
  fa::Automaton fa;
  fa.addState(0);
  EXPECT_FALSE(fa.isValid());
}

TEST(AutomatonTestIsValid, ValidAutomatonWithMultipleObjects) {
  fa::Automaton fa;
  fa.addSymbol('A');
  fa.addSymbol('B');
  fa.addSymbol('C');
  fa.addState(0);
  fa.addState(1);
  EXPECT_TRUE(fa.isValid());
}


////////////////////////////////////// tests pour symbol /////////////////////////////////////////////////

////////////////////////////////////// tests pour addSymbol /////////////////////////////////////////////////

TEST(AutomatonTestAddSymbol, AutomatonWithASymbol) {
  fa::Automaton fa;
  EXPECT_TRUE(fa.addSymbol('A'));
}

TEST(AutomatonTestAddSymbol, AutomatonWithASymbolThatRepetes) {
  fa::Automaton fa;
  EXPECT_TRUE(fa.addSymbol('A'));
  EXPECT_FALSE(fa.addSymbol('A'));
}

TEST(AutomatonTestAddSymbol, AutomatonWithASymbolThatIsABlank) {
  fa::Automaton fa;
  EXPECT_FALSE(fa.addSymbol(' '));
}

TEST(AutomatonTestAddSymbol, AutomatonWithEpsilon) {
  fa::Automaton fa;
  EXPECT_FALSE(fa.addSymbol(fa::Epsilon));
}

////////////////////////////////////// tests pour removeSymbol /////////////////////////////////////////////////

TEST(AutomatonTestRemoveSymbol, AutomatonWithASymbol) {
  fa::Automaton fa;
  fa.addSymbol('A');
  EXPECT_TRUE(fa.removeSymbol('A'));
}

TEST(AutomatonTestRemoveSymbol, AutomatonWithNoSymbol) {
  fa::Automaton fa;
  EXPECT_FALSE(fa.removeSymbol('A'));
}

TEST(AutomatonTestRemoveSymbol, AutomatonWithEraseThatRepetes) {
  fa::Automaton fa;
  fa.addSymbol('A');
  EXPECT_TRUE(fa.removeSymbol('A'));
  EXPECT_FALSE(fa.removeSymbol('A'));
}

////////////////////////////////////// tests pour hasSymbol /////////////////////////////////////////////////

TEST(AutomatonTestHasSymbol, AutomatonWithASymbol) {
  fa::Automaton fa;
  fa.addSymbol('A');
  EXPECT_TRUE(fa.hasSymbol('A'));
}

TEST(AutomatonTestHasSymbol, AutomatonWithIncorrectSymbol) {
  fa::Automaton fa;
  fa.addSymbol('A');
  EXPECT_FALSE(fa.hasSymbol('B'));
}

TEST(AutomatonTestHasSymbol, AutomatonWithEpsilon) {
  fa::Automaton fa;
  fa.addSymbol('A');
  EXPECT_FALSE(fa.hasSymbol(fa::Epsilon));
}

////////////////////////////////////// tests pour countSymbols /////////////////////////////////////////////////

TEST(AutomatonTestCountSymbol, AutomatonWithASymbol) {
  fa::Automaton fa;
  fa.addSymbol('A');
  EXPECT_EQ(1u,fa.countSymbols());
}

TEST(AutomatonTestCountSymbol, AutomatonWithMultipleSymbol) {
  fa::Automaton fa;
  fa.addSymbol('A');
  fa.addSymbol('B');
  fa.addSymbol('C');
  EXPECT_EQ(3u,fa.countSymbols());
}

////////////////////////////////////// tests pour state /////////////////////////////////////////////////

////////////////////////////////////// tests pour addState /////////////////////////////////////////////////

TEST(AutomatonTestAddState, AutomatonWithAState) {
  fa::Automaton fa;
  EXPECT_TRUE(fa.addState(0));
}

TEST(AutomatonTestAddState, AutomatonWithAStateThatRepetes) {
  fa::Automaton fa;
  EXPECT_TRUE(fa.addState(0));
  EXPECT_FALSE(fa.addState(0));
}
////////////////////////////////////// tests pour removeState /////////////////////////////////////////////////

TEST(AutomatonTestRemoveState, AutomatonWithAState) {
  fa::Automaton fa;
  fa.addState(0);
  EXPECT_TRUE(fa.removeState(0));
}

TEST(AutomatonTestRemoveState, AutomatonWithNoState) {
  fa::Automaton fa;
  EXPECT_FALSE(fa.removeState(0));
}

TEST(AutomatonTestRemoveState, AutomatonWithEraseThatRepetes) {
  fa::Automaton fa;
  fa.addState(0);
  EXPECT_TRUE(fa.removeState(0));
  EXPECT_FALSE(fa.removeState(0));
}

//test removeState mis à jour avec le traitement de transition



TEST(AutomatonTestRemoveState, AutomatonWithStateAndTransition) {
  fa::Automaton fa;
  fa.addSymbol('A');
  fa.addState(0);
  fa.addState(1);
  fa.addTransition(0,'A',1);

  EXPECT_TRUE(fa.hasTransition(0,'A',1));
  EXPECT_TRUE(fa.removeState(0));
  EXPECT_FALSE(fa.hasTransition(0,'A',1));
}

TEST(AutomatonTestRemoveState, AutomatonWithStateAndTransition2) {
  fa::Automaton fa;
  fa.addSymbol('A');
  fa.addState(0);
  fa.addState(1);
  fa.addTransition(1,'A',0);

  EXPECT_TRUE(fa.hasTransition(1,'A',0));
  EXPECT_TRUE(fa.removeState(1));
  EXPECT_FALSE(fa.hasTransition(1,'A',0));
}

////////////////////////////////////// tests pour hasState /////////////////////////////////////////////////


TEST(AutomatonTestHasState, AutomatonWithAState) {
  fa::Automaton fa;
  fa.addState(0);
  EXPECT_TRUE(fa.hasState(0));
}

TEST(AutomatonTestHasState, AutomatonWithIncorrectState) {
  fa::Automaton fa;
  fa.addState(0);
  EXPECT_FALSE(fa.hasState(1));
}

////////////////////////////////////// tests pour countStates /////////////////////////////////////////////////

TEST(AutomatonTestCountStates, AutomatonWithAState) {
  fa::Automaton fa;
  fa.addState(0);
  EXPECT_EQ(1u,fa.countStates());
}

TEST(AutomatonTestCountStates, AutomatonWithMultipleState) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  EXPECT_EQ(3u,fa.countStates());
}

////////////////////////////////////// tests pour initial state /////////////////////////////////////////////////

TEST(AutomatonTestSetStateInitialAndIsStateInitial, AutomatonWithAStateInitial) {
  fa::Automaton fa;
  fa.addState(0);
  fa.setStateInitial(0);
  EXPECT_TRUE(fa.isStateInitial(0));
}

TEST(AutomatonTestSetStateInitialAndIsStateInitial, AutomatonWithAStateFinalFalse) {
  fa::Automaton fa;
  fa.addState(0);
  fa.setStateFinal(0);
  EXPECT_FALSE(fa.isStateInitial(0));
}

TEST(AutomatonTestSetStateInitialAndIsStateInitial, AutomatonWithoutState) {
  fa::Automaton fa;
  fa.setStateInitial(0);
  EXPECT_FALSE(fa.isStateInitial(0));
}

////////////////////////////////////// tests pour final state /////////////////////////////////////////////////

TEST(AutomatonTestSetStateFinalAndIsStateFinal, AutomatonWithAStateFinal) {
  fa::Automaton fa;
  fa.addState(0);
  fa.setStateFinal(0);
  EXPECT_TRUE(fa.isStateFinal(0));
}

TEST(AutomatonTestSetStateFinalAndIsStateFinal, AutomatonWithAStateInitialFalse) {
  fa::Automaton fa;
  fa.addState(0);
  fa.setStateInitial(0);
  EXPECT_FALSE(fa.isStateFinal(0));
}

TEST(AutomatonTestSetStateFinalAndIsStateFinal, AutomatonWithoutState) {
  fa::Automaton fa;
  fa.setStateFinal(0);
  EXPECT_FALSE(fa.isStateFinal(0));
}


////////////////////////////////////// tests pour transitions /////////////////////////////////////////////////


////////////////////////////////////// tests pour addTransition /////////////////////////////////////////////////


TEST(AutomatonTestAddTransition, AutomatonWithATransitionButNoSymbolAndState) {
  fa::Automaton fa;
  EXPECT_FALSE(fa.addTransition(0,'A',0));
}

TEST(AutomatonTestAddTransition, AutomatonWithATransitionButNoState) {
  fa::Automaton fa;
  fa.addSymbol('A');
  EXPECT_FALSE(fa.addTransition(0,'A',0));
}

TEST(AutomatonTestAddTransition, AutomatonWithATransitionValid) {
  fa::Automaton fa;
  fa.addSymbol('A');
  fa.addState(0);
  EXPECT_TRUE(fa.addTransition(0,'A',0));
}

TEST(AutomatonTestAddTransition, AutomatonWithATransitionButNoSymbol) {
  fa::Automaton fa;
  fa.addState(0);
  EXPECT_FALSE(fa.addTransition(0,'A',0));
}

TEST(AutomatonTestAddTransition, AutomatonWithATransitionButNoSymbolFrom) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  EXPECT_FALSE(fa.addTransition(1,'A',0));
}


TEST(AutomatonTestAddTransition, AutomatonWithATransitionButNoSymbolTo) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  EXPECT_FALSE(fa.addTransition(0,'A',1));
}


TEST(AutomatonTestAddTransition, AutomatonWithATransitionThatRepetes) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  EXPECT_TRUE(fa.addTransition(0,'A',0));
  EXPECT_FALSE(fa.addTransition(0,'A',0));
}

TEST(AutomatonTestAddTransition, AutomatonWithATransitionWithEpsilon) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  EXPECT_TRUE(fa.addTransition(0,'A',0));
  EXPECT_FALSE(fa.addTransition(0,'A',0));
  EXPECT_TRUE(fa.addTransition(0,fa::Epsilon,0));
}

////////////////////////////////////// tests pour removeTransition /////////////////////////////////////////////////

TEST(AutomatonTestRemoveTransition, AutomatonWithATransition) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addTransition(0,'A',0);
  EXPECT_TRUE(fa.removeTransition(0,'A',0));
}

TEST(AutomatonTestRemoveTransition, AutomatonWithoutATransition) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  EXPECT_FALSE(fa.removeTransition(0,'A',0));
}

TEST(AutomatonTestRemoveTransition, AutomatonWithMultipleTransition) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.addTransition(0,'A',0);
  fa.addTransition(1,'A',0);
  fa.addTransition(1,'A',1);
  EXPECT_TRUE(fa.removeTransition(0,'A',0));
}

TEST(AutomatonTestRemoveTransition, AutomatonWithMultipleTransitionButNoMatchingOne) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.addTransition(0,'A',1);
  fa.addTransition(1,'A',0);
  EXPECT_FALSE(fa.removeTransition(1,'A',1));
}

TEST(AutomatonTestRemoveTransition, AutomatonWithEraseThatRepetes) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.addTransition(0,'A',0);
  fa.addTransition(1,'A',0);
  fa.addTransition(1,'A',1);
  EXPECT_TRUE(fa.removeTransition(1,'A',1));
  EXPECT_FALSE(fa.removeTransition(1,'A',1));
}

////////////////////////////////////// tests pour hasTransition /////////////////////////////////////////////////

TEST(AutomatonTestHasTransition, AutomatonWithAransition) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addTransition(0,'A',0);
  EXPECT_TRUE(fa.hasTransition(0,'A',0));
}

TEST(AutomatonTestHasTransition, AutomatonWithATransitionAndIncorrectKey) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.addTransition(0,'A',0);
  EXPECT_FALSE(fa.hasTransition(1,'A',0));
}

TEST(AutomatonTestHasTransition, AutomatonWithATransitionAndIncorrectTo) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.addTransition(0,'A',0);
  EXPECT_FALSE(fa.hasTransition(0,'A',1));
}

TEST(AutomatonTestHasTransition, AutomatonWithMultipleTransition) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.addTransition(0,'A',0);
  fa.addTransition(1,'A',0);
  fa.addTransition(1,'A',1);
  EXPECT_TRUE(fa.hasTransition(1,'A',0));
}

TEST(AutomatonTestHasTransition, AutomatonWithMultipleTransitionAndIncorrectHas) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.addTransition(0,'A',0);
  fa.addTransition(1,'A',0);
  fa.addTransition(1,'A',1);
  EXPECT_FALSE(fa.hasTransition(1,'A',2));
}

TEST(AutomatonTestHasTransition, AutomatonWithMultipleTransitionAndIncorrectHasAndIncorrectKey) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.addTransition(0,'A',0);
  fa.addTransition(1,'A',0);
  fa.addTransition(1,'A',1);
  EXPECT_FALSE(fa.hasTransition(2,'A',2));
}

////////////////////////////////////// tests pour countTransitions /////////////////////////////////////////////////

TEST(AutomatonTestCountTransitions, AutomatonWithATransition) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.addTransition(0,'A',0);
  EXPECT_EQ(1u,fa.countTransitions());
}

TEST(AutomatonTestCountTransitions, AutomatonWithNoTransition) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  EXPECT_EQ(0u,fa.countTransitions());
}


TEST(AutomatonTestCountTransitions, AutomatonWithMultipleTransition) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.addTransition(0,'A',0);
  fa.addTransition(1,'A',0);
  fa.addTransition(1,'A',1);
  EXPECT_EQ(3u,fa.countTransitions());
}


////////////////////////////////////// tests pour prettyPrint /////////////////////////////////////////////////

TEST(AutomatonTestPrettyPrint, AutomatonWithMultipleTransition) {
  fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.setStateInitial(0);
  fa.setStateInitial(1);
  fa.setStateFinal(1);
  fa.setStateFinal(4);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'a',2);
  fa.addTransition(0,'a',3);
  fa.addTransition(1,'b',3);
  fa.addTransition(2,'a',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'a',4);
  fa.addTransition(4,'a',4);
  fa.prettyPrint(std::cout);
}

TEST(AutomatonTestPrettyPrint, AutomatonWithNoTransition) {
  fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.setStateInitial(0);
  fa.setStateInitial(1);
  fa.setStateFinal(1);
  fa.setStateFinal(4);
  fa.prettyPrint(std::cout);
}

TEST(AutomatonTestPrettyPrint, AutomatonWithNoState) {
  fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.prettyPrint(std::cout);
}

TEST(AutomatonTestPrettyPrint, AutomatonWithNoSymbol) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.setStateInitial(0);
  fa.setStateInitial(1);
  fa.setStateFinal(1);
  fa.setStateFinal(4);
  fa.prettyPrint(std::cout);
}

TEST(AutomatonTestPrettyPrint, AutomatonWithNoStateInitialOrFinal) {
  fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'a',2);
  fa.addTransition(0,'a',3);
  fa.addTransition(1,'b',3);
  fa.addTransition(2,'a',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'a',4);
  fa.addTransition(4,'a',4);
  fa.prettyPrint(std::cout);
}

TEST(AutomatonTestPrettyPrint, AutomatonWithNothing) {
  fa::Automaton fa;
  fa.prettyPrint(std::cout);
}


////////////////////////////////////// tests pour hasEpsilonTransition /////////////////////////////////////////////////

TEST(AutomatonTestHasEpsilonTransition, AutomatonWithAEpsilonTransition) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.addTransition(0,'A',1);
  fa.addTransition(0,fa::Epsilon,0);
  EXPECT_TRUE(fa.hasEpsilonTransition());
}

TEST(AutomatonTestHasEpsilonTransition, AutomatonWithNoEpsilonTransition) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.addTransition(0,'A',1);
  EXPECT_FALSE(fa.hasEpsilonTransition());
}


////////////////////////////////////// tests pour isDeterministic /////////////////////////////////////////////////

TEST(AutomatonTestIsDeterministic, AutomatonWithThatIsDeterministicWithInitial) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.setStateInitial(1);
  fa.addTransition(0,'A',1);
  EXPECT_TRUE(fa.isDeterministic());
}

TEST(AutomatonTestIsDeterministic, AutomatonWithThatIsNotDeterministicBecauseNoneInitial) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addSymbol('B');
  fa.addState(1);
  fa.addTransition(0,'A',1);
  fa.addTransition(0,'B',1);
  EXPECT_FALSE(fa.isDeterministic());
}


TEST(AutomatonTestIsDeterministic, AutomatonWithThatIsNotDeterministicBecauseTwiceLinkA) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addState(1);
  fa.setStateInitial(0);
  fa.addTransition(0,'A',1);
  fa.addTransition(0,'A',0);
  EXPECT_FALSE(fa.isDeterministic());
}

TEST(AutomatonTestIsDeterministic, AutomatonWithThatIsNotDeterministicBecauseMulitpleInitial) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addSymbol('B');
  fa.addState(1);
  fa.setStateInitial(0);
  fa.setStateInitial(1);
  fa.addTransition(0,'A',1);
  fa.addTransition(0,'B',0);
  EXPECT_FALSE(fa.isDeterministic());
}
TEST(AutomatonTestIsDeterministic, AutomatonWithThatIsDeterministicWithInitialAndFinal) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addSymbol('A');
  fa.addSymbol('B');
  fa.addState(1);
  fa.setStateInitial(0);
  fa.setStateFinal(1);
  fa.addTransition(0,'A',1);
  fa.addTransition(0,'B',0);
  EXPECT_TRUE(fa.isDeterministic());
}



////////////////////////////////////// tests pour isComplete /////////////////////////////////////////////////

TEST(AutomatonTestIsComplete, AutomatonWithThatIsComplete) {
   fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'a',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'b',2);
  fa.addTransition(4,'a',4);
  fa.addTransition(4,'b',3);
  EXPECT_TRUE(fa.isComplete());
}

TEST(AutomatonTestIsComplete, AutomatonWithThatIsNotCompleteBecause3DontHaveA) {
   fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'a',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'a',2);
  fa.addTransition(4,'a',4);
  fa.addTransition(4,'b',3);
  EXPECT_FALSE(fa.isComplete());
}

TEST(AutomatonTestIsComplete, AutomatonWithThatIsNotCompleteBecauseAStateWithoutTransition) {
   fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'b',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'a',2);
  fa.addTransition(4,'a',4);
  fa.addTransition(4,'b',3);
  EXPECT_FALSE(fa.isComplete());
}


////////////////////////////////////// tests pour createComplete /////////////////////////////////////////////////

TEST(AutomatonTestCreateComplete, AutomatonThatIsNotCompleteBecauseAStateWithoutTransition) {
   fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'b',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'a',2);
  fa.addTransition(4,'a',4);
  fa.addTransition(4,'b',3);
  EXPECT_FALSE(fa.isComplete());
  fa::Automaton fa_complete = fa::Automaton::createComplete(fa);
  EXPECT_TRUE(fa_complete.isComplete());
}


TEST(AutomatonTestCreateComplete, AutomatonAlreadyComplete) {
   fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'a',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'b',2);
  fa.addTransition(4,'a',4);
  fa.addTransition(4,'b',3);
  EXPECT_TRUE(fa.isComplete());
}

TEST(AutomatonTestCreateComplete, AutomatonTestedWithMatch) {
   fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.setStateInitial(0);
  fa.setStateFinal(4);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'b',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'b',4);
  fa.addTransition(4,'a',4);
  fa.addTransition(4,'b',3);
  EXPECT_FALSE(fa.isComplete());
  fa::Automaton fa_complete = fa::Automaton::createComplete(fa);
  EXPECT_TRUE(fa_complete.isComplete());

  //test avec match

  EXPECT_TRUE(fa.match("abaaaaba"));
  EXPECT_TRUE(fa_complete.match("abaaaaba"));
}

////////////////////////////////////// tests pour createComplement /////////////////////////////////////////////////

TEST(AutomatonTestCreateComplement, AutomatonThatIsNotComplete) {
   fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.setStateInitial(0);
  fa.setStateFinal(4);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'b',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'b',4);
  fa.addTransition(4,'a',4);
  fa.addTransition(4,'b',3);
  fa::Automaton fa_complement = fa::Automaton::createComplement(fa);
  EXPECT_TRUE(fa.match("abaaaaba"));
  EXPECT_FALSE(fa_complement.match("abaaaaba"));
  EXPECT_FALSE(fa.match("a"));
  EXPECT_TRUE(fa_complement.match("a"));
}


TEST(AutomatonCreateComplement, AutomatonWithThatIsComplete) {
  fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.setStateInitial(0);
  fa.setStateFinal(4);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'a',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'b',4);
  fa.addTransition(4,'a',4);
  fa.addTransition(4,'b',3);
  fa::Automaton fa_complement = fa::Automaton::createComplement(fa);
  EXPECT_TRUE(fa.match("abaaaaba"));
  EXPECT_FALSE(fa_complement.match("abaaaaba"));
  EXPECT_FALSE(fa.match("a"));
  EXPECT_TRUE(fa_complement.match("a"));
}

TEST(AutomatonTestCreateComplement, AutomatonTestedWithMatch) {
   fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.setStateInitial(0);
  fa.setStateFinal(4);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'b',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'b',4);
  fa.addTransition(4,'a',4);
  fa.addTransition(4,'b',3);
  fa::Automaton fa_complement = fa::Automaton::createComplement(fa);
  //test avec match
  EXPECT_TRUE(fa.match("abaaaaba"));
  EXPECT_FALSE(fa_complement.match("abaaaaba"));
  EXPECT_FALSE(fa.match("a"));
  EXPECT_TRUE(fa_complement.match("a"));

}

////////////////////////////////////// tests pour createMirror /////////////////////////////////////////////////


TEST(AutomatonTestCreateMirror, AutomatonThatIsNotCompleteAndInitialAndFinal) {
  fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.setStateInitial(0);
  fa.setStateFinal(4);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'b',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'b',4);
  fa.addTransition(4,'a',4);
  fa.addTransition(4,'b',3);
  fa::Automaton fa_mirror = fa::Automaton::createMirror(fa);

  EXPECT_TRUE(fa.match("abb"));
  EXPECT_TRUE(fa_mirror.match("bba"));
  EXPECT_TRUE(fa.match("abaab"));
  EXPECT_TRUE(fa_mirror.match("baaba"));

}


////////////////////////////////////// tests pour makeTransition /////////////////////////////////////////////////


TEST(AutomatonTestMakeTransition, AutomatonThatIsNotComplete) {
   fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.setStateInitial(0);
  fa.setStateFinal(5);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'b',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',3);
  fa.addTransition(3,'a',2);
  fa.addTransition(4,'a',4);
  fa.addTransition(4,'b',3);
  std::set<int> origin ={0,1,2,3};
  std::set<int> expected = fa.makeTransition(origin,'b');
  EXPECT_TRUE(expected.find(0)!=expected.end());
  EXPECT_TRUE(expected.find(3)!=expected.end());
  EXPECT_TRUE(expected.find(4)!=expected.end());
}


////////////////////////////////////// tests pour readString /////////////////////////////////////////////////


TEST(AutomatonTestReadString, AutomatonThatIsNotCompleteAndWeFinishOnAFinalState) {
   fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.setStateInitial(0);
  fa.setStateFinal(5);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'b',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',4);
  fa.addTransition(3,'a',4);
  fa.addTransition(4,'a',5);
  fa.addTransition(4,'b',3);
  std::string word = "aabaa";
  std::set<int> result = fa.readString(word);
  EXPECT_FALSE(result.find(1)!=result.end());
  EXPECT_FALSE(result.find(2)!=result.end());
  EXPECT_FALSE(result.find(3)!=result.end());
  EXPECT_FALSE(result.find(4)!=result.end());
  EXPECT_TRUE(result.find(5)!=result.end());
}




TEST(AutomatonTestReadString, AutomatonThatIsNotCompleteAndWeNotFinishOnAFinal) {
  fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.setStateInitial(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.setStateFinal(5);
  fa.addTransition(0,'a',0);
  fa.addTransition(0,'a',1);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'b',2);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'b',3);
  std::string word = "abb";
  std::set<int> result = fa.readString(word);
  EXPECT_FALSE(result.find(1)!=result.end());
  EXPECT_FALSE(result.find(2)!=result.end());
  EXPECT_TRUE(result.find(3)!=result.end());
  EXPECT_TRUE(result.find(4)!=result.end());
  EXPECT_FALSE(result.find(5)!=result.end());


}



////////////////////////////////////// tests pour match /////////////////////////////////////////////////


TEST(AutomatonTestMatch, AutomatonThatDoesRecognizeTheWord) {
   fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.setStateInitial(0);
  fa.setStateFinal(5);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'b',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',4);
  fa.addTransition(3,'a',4);
  fa.addTransition(4,'a',5);
  fa.addTransition(4,'b',3);
  std::string word = "aabaa";
  EXPECT_TRUE(fa.match(word));
}


TEST(AutomatonTestMatch, AutomatonThatDoesNotRecognizeTheWord) {
  fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.setStateInitial(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.setStateFinal(5);
  fa.addTransition(0,'a',0);
  fa.addTransition(0,'a',1);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'b',2);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'b',3);
  std::string word = "abb";
  EXPECT_FALSE(fa.match(word));


}


////////////////////////////////////// tests pour isLanguageEmpty /////////////////////////////////////////////////



TEST(AutomatonTestIsLanguageEmpty, AutomatonThatIsEmptyButWithATransition) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.setStateInitial(0);
  fa.setStateFinal(2);
  fa.addSymbol('a');
  fa.addTransition(0,'a',1);

  EXPECT_TRUE(fa.isLanguageEmpty());
}


TEST(AutomatonTestIsLanguageEmpty, AutomatonThatIsNotEmpty) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.setStateInitial(0);
  fa.setStateFinal(2);
  fa.addSymbol('a');
  fa.addTransition(0,'a',1);
  fa.addTransition(1,'a',2);

  EXPECT_FALSE(fa.isLanguageEmpty());
}


TEST(AutomatonTestIsLanguageEmpty, AutomatonThatIsNotEmptyAndMoreComplex) {
   fa::Automaton fa;
  fa.addSymbol('a');
  fa.addSymbol('b');
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.setStateInitial(0);
  fa.setStateFinal(5);
  fa.addTransition(0,'a',1);
  fa.addTransition(0,'b',0);
  fa.addTransition(1,'b',3);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'b',3);
  fa.addTransition(2,'b',4);
  fa.addTransition(3,'a',4);
  fa.addTransition(3,'a',4);
  fa.addTransition(4,'a',5);
  fa.addTransition(4,'b',3);
  EXPECT_FALSE(fa.isLanguageEmpty());
}


////////////////////////////////////// tests pour removeNonAccessibleStates /////////////////////////////////////////////////



TEST(AutomatonTestRemoveNonAccessibleStates, Automaton) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.setStateInitial(0);
  fa.setStateFinal(2);
  fa.addSymbol('a');
  fa.addTransition(0,'a',1);
  fa.addTransition(1,'a',2);
  fa.removeNonAccessibleStates();
  EXPECT_FALSE(fa.hasState(3));

 
}

TEST(AutomatonTestRemoveNonAccessibleStates, AutomatonWithNoTransition) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.setStateInitial(0);
  fa.setStateFinal(2);
  fa.addSymbol('a');
  fa.removeNonAccessibleStates();
  EXPECT_FALSE(fa.hasState(3));
  EXPECT_FALSE(fa.hasState(2));
  EXPECT_FALSE(fa.hasState(1));
  EXPECT_TRUE(fa.hasState(0));
}

TEST(AutomatonTestRemoveNonAccessibleStates, AutomatonATransitionButNoFinal) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addState(1);
  fa.setStateInitial(0);
  fa.addSymbol('a');
  fa.addTransition(0,'a',1);
  fa.removeNonAccessibleStates();
  EXPECT_TRUE(fa.hasState(1));
  EXPECT_TRUE(fa.hasState(0));
}

TEST(AutomatonTestRemoveNonAccessibleStates, AutomatonATransitionAndAFinal) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addState(1);
  fa.setStateInitial(0);
  fa.setStateFinal(1);
  fa.addSymbol('a');
  fa.addTransition(0,'a',1);
  fa.removeNonAccessibleStates();
  EXPECT_TRUE(fa.hasState(1));
  EXPECT_TRUE(fa.hasState(0));
}



////////////////////////////////////// tests pour removeNonCoAccessibleStates /////////////////////////////////////////////////



TEST(AutomatonTestRemoveNonCoAccessibleStates, AutomatonWithoutNonCoAccessibleStates) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.setStateInitial(0);
  fa.setStateFinal(2);
  fa.addSymbol('a');
  fa.addTransition(0,'a',1);
  fa.addTransition(1,'a',2);
  fa.addTransition(3,'a',2);
  fa.removeNonCoAccessibleStates();
  EXPECT_TRUE(fa.hasState(3));

 
}



TEST(AutomatonTestRemoveNonCoAccessibleStates, AutomatonWithNonCoAccessibleStates) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.setStateInitial(0);
  fa.setStateFinal(2);
  fa.addSymbol('a');
  fa.addTransition(0,'a',1);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'a',3);
  fa.removeNonCoAccessibleStates();
  EXPECT_FALSE(fa.hasState(3));

}


TEST(AutomatonTestRemoveNonCoAccessibleStates, AutomatonWithNonCoAccessibleStates2) {
  fa::Automaton fa;
  fa.addState(0);
  fa.addState(1);
  fa.addState(2);
  fa.addState(3);
  fa.addState(4);
  fa.addState(5);
  fa.addState(6);
  fa.setStateInitial(0);
  fa.setStateFinal(6);
  fa.addSymbol('a');
  fa.addTransition(0,'a',1);
  fa.addTransition(1,'a',2);
  fa.addTransition(2,'a',4);
  fa.addTransition(3,'a',5);
  fa.addTransition(4,'a',6);
  fa.addTransition(6,'a',5);
  fa.removeNonCoAccessibleStates();
  EXPECT_FALSE(fa.hasState(5));
  EXPECT_FALSE(fa.hasState(3));

}


////////////////////////////////////// tests pour createIntersection /////////////////////////////////////////////////

//langage des mots qui commencent par un a et finissent par un b
TEST(AutomatonTestCreateIntersection, AutomatonsOfTheCM) {
  fa::Automaton fa1;
  fa::Automaton fa2;
  fa1.addState(0);
  fa1.addState(1);
  fa2.addState(0);
  fa2.addState(1);
  fa1.setStateInitial(0);
  fa1.setStateFinal(1);
  fa2.setStateInitial(0);
  fa2.setStateFinal(1);
  fa1.addSymbol('a');
  fa1.addSymbol('b');
  fa2.addSymbol('a');
  fa2.addSymbol('b');
  fa1.addTransition(0,'a',1);
  fa1.addTransition(1,'a',1);
  fa1.addTransition(1,'b',1);
  fa2.addTransition(0,'b',1);
  fa2.addTransition(1,'b',1);
  fa2.addTransition(0,'a',0);
  fa2.addTransition(1,'a',0);
  fa::Automaton fa = fa::Automaton::createIntersection(fa1,fa2);
  std::string word1 = "ab";
  std::string word2 = "abbbbbbb";
  std::string word3 = "b";
  std::string word4 = "a";
  EXPECT_TRUE(fa.match(word1));
  EXPECT_TRUE(fa.match(word2));
  EXPECT_FALSE(fa.match(word3));
  EXPECT_FALSE(fa.match(word4));


}

TEST(AutomatonTestCreateIntersection, AutomatonsThatAreExactlyTheSame) {
  fa::Automaton fa1;
  fa::Automaton fa2;
  fa1.addState(0);
  fa1.addState(1);
  fa2.addState(0);
  fa2.addState(1);
  fa1.setStateInitial(0);
  fa1.setStateFinal(1);
  fa2.setStateInitial(0);
  fa2.setStateFinal(1);
  fa1.addSymbol('a');
  fa1.addSymbol('b');
  fa2.addSymbol('a');
  fa2.addSymbol('b');
  fa1.addTransition(0,'a',1);
  fa1.addTransition(1,'a',1);
  fa1.addTransition(1,'b',1);
  fa2.addTransition(0,'a',1);
  fa2.addTransition(1,'a',1);
  fa2.addTransition(1,'b',1);
  fa::Automaton fa = fa::Automaton::createIntersection(fa1,fa2);
  std::string word1 = "ab";
  std::string word2 = "abbbbbbb";
  std::string word3 = "b";
  std::string word4 = "a";
  EXPECT_TRUE(fa1.match(word1));
  EXPECT_TRUE(fa1.match(word2));
  EXPECT_FALSE(fa1.match(word3));
  EXPECT_TRUE(fa1.match(word4));
  EXPECT_TRUE(fa2.match(word1));
  EXPECT_TRUE(fa2.match(word2));
  EXPECT_FALSE(fa2.match(word3));
  EXPECT_TRUE(fa2.match(word4));
  EXPECT_TRUE(fa.match(word1));
  EXPECT_TRUE(fa.match(word2));
  EXPECT_FALSE(fa.match(word3));
  EXPECT_TRUE(fa.match(word4));
 




}


////////////////////////////////////// tests pour hasEmptyIntersectionWith /////////////////////////////////////////////////

TEST(AutomatonTestHasEmptyIntersectionWith, AutomatonsThatHaveCompletelyDifferentAlphabet) {
  fa::Automaton fa1;
  fa::Automaton fa2;
  fa1.addState(0);
  fa1.addState(1);
  fa2.addState(0);
  fa2.addState(1);
  fa1.setStateInitial(0);
  fa1.setStateFinal(1);
  fa2.setStateInitial(0);
  fa2.setStateFinal(1);
  fa1.addSymbol('a');
  fa1.addSymbol('b');
  fa2.addSymbol('c');
  fa2.addSymbol('d');
  fa1.addTransition(0,'a',1);
  fa1.addTransition(1,'a',1);
  fa1.addTransition(1,'b',1);
  fa2.addTransition(0,'b',1);
  fa2.addTransition(1,'b',1);
  fa2.addTransition(0,'a',0);
  fa2.addTransition(1,'a',0);
  EXPECT_TRUE(fa1.hasEmptyIntersectionWith(fa2));



}

TEST(AutomatonTestHasEmptyIntersectionWith, AutomatonsThatHaveTheSameAlphabet) {
  fa::Automaton fa1;
  fa::Automaton fa2;
  fa1.addState(0);
  fa1.addState(1);
  fa2.addState(0);
  fa2.addState(1);
  fa1.setStateInitial(0);
  fa1.setStateFinal(1);
  fa2.setStateInitial(0);
  fa2.setStateFinal(1);
  fa1.addSymbol('a');
  fa1.addSymbol('b');
  fa2.addSymbol('a');
  fa2.addSymbol('b');
  fa1.addTransition(0,'a',1);
  fa1.addTransition(1,'a',1);
  fa1.addTransition(1,'b',1);
  fa2.addTransition(0,'b',1);
  fa2.addTransition(1,'b',1);
  fa2.addTransition(0,'a',0);
  fa2.addTransition(1,'a',0);
  EXPECT_FALSE(fa1.hasEmptyIntersectionWith(fa2));



}

TEST(AutomatonTestHasEmptyIntersectionWith, AutomatonsThatAreExactlyTheSame) {
  fa::Automaton fa1;
  fa::Automaton fa2;
  fa1.addState(0);
  fa1.addState(1);
  fa2.addState(0);
  fa2.addState(1);
  fa1.setStateInitial(0);
  fa1.setStateFinal(1);
  fa2.setStateInitial(0);
  fa2.setStateFinal(1);
  fa1.addSymbol('a');
  fa1.addSymbol('b');
  fa2.addSymbol('a');
  fa2.addSymbol('b');
  fa1.addTransition(0,'a',1);
  fa1.addTransition(1,'a',1);
  fa1.addTransition(1,'b',1);
  fa2.addTransition(0,'a',1);
  fa2.addTransition(1,'a',1);
  fa2.addTransition(1,'b',1);
 
  EXPECT_FALSE(fa1.hasEmptyIntersectionWith(fa2));



}

int main(int argc, char **argv) {
  ::testing::InitGoogleTest(&argc, argv);
  return RUN_ALL_TESTS();
}
