const draggable = require('./draggable.js');

window.CardManager = function(type) {
    this.type = type;
    this.status = 'off';

    this.counter = 0;
    this.counterCorrectAnswer = 0;
    this.counterWrongAnswer = 0;

    this.previousRule = '';
    this.currentRule = '';
    this.ruleOrder = []

    this.cardTimer = null;
    this.testTimer = null;

    this.partie = {
        'tempsNecessaire': null,
        'choixCartes': [],
        'changementsRegles': []
    };
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

    $(document).on('click', 'body .card.flippable', function (e) {
        $(this).toggleClass('flipped');
        if($(this).hasClass('flippable-once')) {
            $(this).removeClass('flippable');
        }
    });

    $('.btn-ok').click(function() {
        $(this).closest('.ok-container').hide();
    });
};

/**
 * Démarre la partie
 */
CardManager.prototype.start = function() {
    let self = this;

    this.testTimer = new Date();

    $('#explications').hide();
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
        nextCard.append('<div class="msg-flip-card">RETOURNER</div>');
        nextCard.click(function() {
            $(this).find('.msg-flip-card').remove();
            self.flipNextCard($(this));
        });
    } else {
        // fin de la partie
        this.end();
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

    $('#result div').hide();
    $('#matchers .card-pile .card').addClass('flipped').removeClass('flippable');
    $('#matchers .card-pile .card').not('.last-card').addClass('flipped-forever')
    $('.last-card').addClass('flippable').removeClass('flippable-once');

    if(this.type === 'glisser') {
        let draggableCard = document.getElementById('deck-card');
        draggableCard.classList.add('draggable');
        draggable(draggableCard, this);
    }

    this.cardTimer = new Date();
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
    let diff = (this.cardTimer.getTime() - new Date().getTime());

    // Construction d'une réponse
    let choixCarte = {};
    choixCarte.tempsMicrosecondes = Math.abs(diff);

    let cardId = cardMatcher.attr('id').replace('matcher-', '');

    // Carte déposée en dessous d'une carte de référence
    let card = currentDeckCard.clone();
    card[0].style.left = 0;
    card[0].style.top = 0;
    card.attr('id', null);
    card.removeClass('highlight draggable');
    cardMatcher.append(card);
    $('.last-card').removeClass('last-card');
    card.addClass('last-card');

    // Recherche des règles matchées entre deux cartes
    let matchedCard = $('#to-match .card-pile[data-reference=' + cardId + '] .card');
    let correct = false;

    // Deux cartes matcheront toujours une seule règle (le tirage est prévu pour)
    let matchedRule = this.getMatchingRulesBetweenCards(currentDeckCard, matchedCard);

    // Application des exigences du test suivant la règle trouvée entre les deux cartes
    if(matchedRule !== '') {
        if(this.currentRule === '' && this.partie.choixCartes.length === 0) {
            // Première carte déposée
            // La règle matchée devient la règle actuelle pour les prochaines cartes
            correct = true;
            this.currentRule = matchedRule;
        } else if (this.currentRule === '') {
            // Nouvelle règle imposée
            // On vérifie si l'ordre des règles est établie ou pas encore totalement (ex: forme, puis couleur, puis nombre)
            if(this.ruleOrder.length < 3) {
                if(matchedRule !== this.previousRule && !this.ruleOrder.includes(matchedRule)) {
                    // Si c'est une règle pas encore établie, on la considère comme correcte
                    this.currentRule = matchedRule;
                    correct = true;
                }
            } else {
                // L'ordre des règles est déjà établie, on est donc au moins au 3ème changement de règle.
                let matchedRuleOrderIndex = this.ruleOrder.findIndex((element) => element === matchedRule);
                if(matchedRuleOrderIndex === this.partie.changementsRegles.length - 3) {
                    this.currentRule = matchedRule;
                    correct = true;
                }
            }
        } else if (matchedRule === this.currentRule) {
            correct = true;
        }
    }

    // Sauvegarde du code de la carte de référence et de la carte posée
    choixCarte.carteReference = matchedCard.attr('data-code');
    choixCarte.cartePosee = currentDeckCard.attr('data-code');

    // Comportement si vrai ou faux
    let right = $('#result .right').hide();
    let wrong = $('#result .wrong').hide();
    let ruleChange = $('#result .change').hide();
    if(correct === true) {
        this.counterCorrectAnswer++;
        this.counterWrongAnswer = 0;
        right.show();
        wrong.hide();
    } else {
        this.counterWrongAnswer++;
        this.counterCorrectAnswer = 0;
        wrong.show();
        right.hide();
    }

    // Sauvegarde du si bonne ou mauvaise réponse
    choixCarte.isBonneReponse = correct;
    choixCarte.regleActuelle = this.currentRule;
    this.partie.choixCartes.push(choixCarte);

    if(this.counterCorrectAnswer === 6) {
        // Changement de règle
        this.partie.changementsRegles.push({'index': this.partie.choixCartes.length + 1, 'lastRule': this.lastRule, 'rule': this.currentRule});

        let lastRule = this.currentRule;
        if(this.ruleOrder.length < 3) {
            this.ruleOrder.push(lastRule)
        }

        this.counterCorrectAnswer = 0;
        $('#nouvelle-regle').show();

        this.currentRule = '';
    }

    if(this.counterWrongAnswer === 6) {
        // TODO ré-affichage des règles
        this.counterWrongAnswer = 0;
    }

    if(this.partie.changementsRegles.length === 6) {
        this.end();
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
CardManager.prototype.getMatchingRulesBetweenCards = function(cardOne, cardTwo) {
    let codeFirstCard = cardOne.attr('data-code');
    let codeSecondCard = cardTwo.attr('data-code');

    if(codeFirstCard[0] === codeSecondCard[0]){
        return 'nombre';
    }
    if(codeFirstCard[1] === codeSecondCard[1]){
        return 'forme';
    }
    if(codeFirstCard[2] === codeSecondCard[2]){
        return 'couleur';
    }
    return '';
};

/**
 * Fin du test en envoie des données en bdd
 */
CardManager.prototype.end = function() {
    let diff = (this.testTimer.getTime() - new Date().getTime());
    this.partie.tempsNecessaire = Math.abs(diff);

    $('#nouvelle-regle').hide();
    $("#loader").show();
    console.log(this.partie)

    self.currentRequest = $.ajax({
        url: '/save-test',
        data: {'partie': this.partie},
        type: 'POST',
        complete: function(response) {
            window.location = response.responseText;
        }
    });
};