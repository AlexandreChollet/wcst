const draggable = require('./draggable.js');

window.CardManager = function(type) {
    this.type = type;
    this.status = 'off';
    this.currentRule = '';
    this.counter = 0;
    this.counterCorrectAnswer = 0;
    this.counterWrongAnswer = 0;
    this.rules = ['chiffre', 'forme', 'couleur']
};

/**
 * Initialisation
 */
CardManager.prototype.init = function() {
    let self = this;
    self.status = 'on';

    // Lancement de la partie
    $('#btn-go').click(function() {
        self.start();
    });
};

/**
 * Démarre la partie
 */
CardManager.prototype.start = function() {
    let self = this;

    $('#explications').remove();
    $('#deck').show();
    $('#result').show();

    if(this.type === 'click') {
        $('#to-match .card-pile').addClass('clickable').click(function() {
            if(self.status === 'on') {
                //$('#matchers .card-pile .card').hide();
                let matcher = $('#matcher-' + $(this).attr('data-reference'));
                self.placeCard($('#deck-card'), matcher);
            }
        });
    } else {
        //$('#matchers').show();
    }
    $('#matchers').show();

    this.prepareNextCard();
};

/**
 * Active la prochaine carte à drag & drop ou termine la partie
 */
CardManager.prototype.prepareNextCard = function() {
    let self = this;

    let nextCards = $('#deck .card');
    if(nextCards.length > 0) {
        let nextCard = $(nextCards[nextCards.length - 1]);
        nextCard.click(function() {
            self.flipNextCard($(this));
        });
    } else {
        // TODO
        // fin de la partie
        this.status = 'off';
    }

    this.counter++;
};

/**
 * Retourne la prochaine carte
 *
 * @param card
 */
CardManager.prototype.flipNextCard = function(card) {
    // Gestion du drag and drop sur la carte du deck
    card.attr('id', 'deck-card');
    card.removeClass('flipped');

    if(this.type === 'glisser') {
        let draggableCard = document.getElementById('deck-card');
        draggableCard.classList.add('draggable');
        draggable(draggableCard, this);
    }
};

/**
 * Détection du début de clic sur la carte de deck
 */
CardManager.prototype.onMouseDown = function() {
    $('#matchers .card-pile, #deck-card').addClass('highlight');

    this.onMouseMove();
};

/**
 * Détection de la fin de clic sur la carte de deck
 */
CardManager.prototype.onMouseUp = function() {
    let currentDeckCard = $('#deck-card');

    let matchingCards = $('#matchers .card-pile.intersects');
    if(matchingCards.length > 0) {
        // Carte déposée en dessous d'une carte de référence
        this.placeCard(currentDeckCard, matchingCards);
    } else {
        // Reset de la carte
        currentDeckCard.animate({
            top: "0",
            left: "0"
        }, {
            duration: 100,
            complete: function () {
                currentDeckCard[0].style.left = '0px';
                currentDeckCard[0].style.top = '0px';
            }
        });
    }

    $('#matchers .card-pile, #deck-card').removeClass('highlight intersects');
};

/**
 * Détection de chaque mouvement de souris avec la carte
 */
CardManager.prototype.onMouseMove = function() {
    let self = this;
    const classIntersect = 'intersects';

    let cardMatchers = $('#matchers .card-pile');
    let closestMatcher = null;
    let closestDistance = null;

    // Récupération de la carte la plus proche
    let deckCard = $('#deck-card');
    let offsetDeckCard = deckCard.offset();
    let centerDeckCardX = offsetDeckCard.left + deckCard.width() / 2;
    let centerDeckCardY = offsetDeckCard.top + deckCard.height() / 2;
    cardMatchers.each(function() {
        if(self.intersects(this, document.getElementById('deck-card'))) {
            let offset = $(this).offset();
            let centerX = offset.left + $(this).width() / 2;
            let centerY = offset.top + $(this).height() / 2;

            // Calcul de la distance entre deux points
            let distance = Math.sqrt(
                Math.pow((centerDeckCardX - centerX), 2) +
                Math.pow((centerDeckCardY - centerY), 2)
            );

            if(closestDistance === null || distance < closestDistance) {
                // Carte la plus proche trouvée
                closestDistance = distance;
                closestMatcher = this;
            }
        } else {
            if($(this).hasClass(classIntersect)) {
                $(this).removeClass(classIntersect);
            }
        }
    });

    // Suppression du highlight pour les cartes non-matchées
    cardMatchers.each(function() {
        if(this !== closestMatcher && $(this).hasClass(classIntersect)) {
            $(this).removeClass(classIntersect);
        }
    });

    // Ajout du highlight pour la carte matchée
    if(closestMatcher !== null && !$(closestMatcher).hasClass(classIntersect)) {
        $(closestMatcher).addClass(classIntersect);
    }
};

/**
 * Déplace la carte actuelle du deck sous la carte de référence souhaitée
 *
 * @param currentDeckCard
 * @param cardMatcher
 */
CardManager.prototype.placeCard = function(currentDeckCard, cardMatcher) {
    let cardId = cardMatcher.attr('id').replace('matcher-', '');

    // Carte déposée en dessous d'une carte de référence
    let card = currentDeckCard.clone();
    card[0].style.left = 0;
    card[0].style.top = 0;
    card.attr('id', null);
    card.removeClass('highlight draggable');
    cardMatcher.append(card);

    // Application des règles du test en fonction de la règle trouvée entre les deux cartes (s'il y en a une)
    let matchedCard = $('#to-match .card-pile[data-reference=' + cardId + '] .card')
    let correct = false;
    let matchedRule = this.getMatchingRuleBetweenCards(currentDeckCard, matchedCard);
    if(this.currentRule === '') {
        // Première carte déposée
        if(matchedRule !== '') {
            // La règle matchée devient la règle actuelle pour les prochaines cartes
            correct = true;
            this.currentRule = matchedRule;
        }
    } else if (this.currentRule === matchedRule) {
        correct = true;
    }

    // Comportement si vrai ou faux
    let right = $('#result .right').hide();
    let wrong = $('#result .wrong').hide();
    let ruleChange = $('#result .change').hide();
    if(correct === true) {
        this.counterCorrectAnswer++;
        right.show();
        wrong.hide();
    } else {
        this.counterWrongAnswer++;
        wrong.show();
        right.hide();
    }

    if(this.counterCorrectAnswer === 6) {
        // Changement de règle
        let index = this.rules.findIndex(element => this.currentRule === element);
        this.currentRule = this.rules[index === this.rules.length - 1 ? 0 : index + 1];
        this.counterCorrectAnswer = 0;
        console.log(this.currentRule)
        right.hide();
        ruleChange.show();
    }

    if(this.counterWrongAnswer === 6) {
        // TODO ré-affichage des règles
        this.counterWrongAnswer = 0;

    }

    // On passe la main à la carte suivante
    currentDeckCard.remove();
    this.prepareNextCard();
};

/**
 * Détecte une collision entre deux éléments html
 *
 * @param el1
 * @param el2
 * @returns {boolean}
 */
CardManager.prototype.intersects = function(el1, el2) {
    let rect1 = el1.getBoundingClientRect();
    let rect2 = el2.getBoundingClientRect();

    return !(
        rect1.top > rect2.bottom ||
        rect1.right < rect2.left ||
        rect1.bottom < rect2.top ||
        rect1.left > rect2.right
    );
};

/**
 * Trouve une règle qui match les deux cartes
 *
 * @param cardOne
 * @param cardTwo
 */
CardManager.prototype.getMatchingRuleBetweenCards = function(cardOne, cardTwo) {
    let codeFirstCard = cardOne.attr('data-code');
    let codeSecondCard = cardTwo.attr('data-code');

    if(codeFirstCard[0] === codeSecondCard[0]){
        return 'chiffre';
    }
    if(codeFirstCard[1] === codeSecondCard[1]){
        return 'forme';
    }
    if(codeFirstCard[2] === codeSecondCard[2]){
        return 'couleur';
    }
    return '';
};
