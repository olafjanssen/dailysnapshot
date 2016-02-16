/**
 * Created by olafjanssen on 23/01/16.
 */

var outcomeData = [
  {
    title: 'Strategie & Concept',
    description: 'De student kan een concept ontwerpen en uitwerken door gebruik te maken van verschillende methodieken en de visies van andere conceptuele denkers toe te passen. Hij verwerkt hierin een visie op het vakgebied.',
    outcomes: [
      {
        title: 'Strategie en Concept: Visie',
        display_name: 'Strategie en Concept: Visie',
        description: 'Strategie en Concept: Visie',
        calculation_method: 'latest',
        mastery_points: 4,
        ratings: [
          {description: 'Verkennend - Je weet een expert  binnen het medialandschap te benoemen.', points: 1},
          {
            description: 'Werk in uitvoering - Je laat zien door welke expert je geïnspireerd bent geraakt en kunt deze visie verwoorden.',
            points: 2
          },
          {
            description: 'Twijfelachtig - Je hebt matig onderzoek gedaan naar bestaande opvattingen van experts uit het medialandschap en laat minimaal zien door welke visie(s) je geïnspireerd bent geraakt. Je hebt in het studieproces minimale visie op het vakgebied laten zien.',
            points: 3
          },
          {
            description: 'Geoefend - Je hebt onderzoek gedaan naar bestaande opvattingen van experts uit het medialandschap en laat zien door welke visie(s) je geïnspireerd bent geraakt. Je heb in het studieproces een visie geleend op het vakgebied en uit dit in de concepten.',
            points: 4
          },
          {
            description: 'Uitstekend - Je hebt veel onderzoek gedaan naar bestaande opvattingen van experts uit het medialandschap en laat zien door welke visie(s) je geïnspireerd bent geraakt.Je hebt in het studie proces een eigenzinnige visie gevormd op het vakgebied en dit komt tot uiting in de concepten.',
            points: 5
          }
        ]
      },
      {
        title: 'Strategie en Concept: Conceptueel denken',
        display_name: 'Strategie en Concept: Conceptueel denken',
        description: 'Strategie en Concept: Conceptueel denken',
        calculation_method: 'latest',
        mastery_points: 4,
        ratings: [
          {description: 'Verkennend - Je verkent en onderzoekt bestaande ideeën.', points: 1},
          {
            description: 'Werk in uitvoering - Je hebt meerdere ideeën en analyseert deze.',
            points: 2
          },
          {
            description: 'Twijfelachtig - Je laat een concept en POC zien, zonder dat het werkproces of onderbouwing is aangetoond.',
            points: 3
          },
          {
            description: 'Geoefend - Je bent in staat om een gedeelte van het werkproces inzichtelijk te maken en de gemaakte keuzes te laten zien. Ideeën en schetsen dienen bijgedragen te hebben aan het gekozen concept en je kan dit ook omzetten in (meerdere) POC.',
            points: 4
          },
          {
            description: 'Uitstekend - Je bent in staat om het gehele werkproces inzichtelijk te maken, de gemaakte keuzes toe te lichten en te onderbouwen. Een grote hoeveelheid aan   ideeën en schetsen dienen op een logische wijze bijgedragen te hebben aan het gekozen concept en kan dit ook omzetten in (meerdere) sterke POC.',
            points: 5
          }
        ]
      }
    ]
  },
  {
    title: 'User Experience & User Centered Design',
    description: 'De student zet storytelling in voor een beoogde gebruikerservaring en test het behaalde effect door middel van usertests.',
    outcomes: [
      {
        title: 'User Experience en User Centered Design: Storytelling',
        display_name: 'User Experience en User Centered Design: Storytelling',
        description: 'User Experience en User Centered Design: Storytelling',
        calculation_method: 'latest',
        mastery_points: 4,
        ratings: [
          {description: 'Verkennend - Je weet welke elementen en verhaalstructuren nodig zijn om een verhaal goed te kunnen vertellen.', points: 1},
          {
            description: 'Werk in uitvoering - Je bedenkt en omschrijft een verhaal en realiseert het in een schetsmatige vorm, bijvoorbeeld met een (moving) storyboard.',
            points: 2
          },
          {
            description: 'Twijfelachtig - Je bedenkt en omschrijft een verhaal. Het verhaal is  schetsmatig gerealiseerd, maar er is nog niet nagedacht over de media uitingen waarop het gepresenteerd wordt.',
            points: 3
          },
          {
            description: 'Geoefend - Je verhaal is opgebouwd uit visuele elementen en de verhaalstructuur is terug te zien. Het is duidelijk wat de maker wil communiceren. Je hebt gekozen voor een techniek met bijbehorende media.',
            points: 4
          },
          {
            description: 'Uitstekend - Je verhaal is naast visuele elementen opgebouwd uit een duidelijke en originele verhaalstructuur. Het werkproces is inzichtelijk in kaart gebracht. Het moet de bedoeling van de maker helder en gestructureerd overbrengen. Dit gebeurt met verschillende media uitingen en technieken.',
            points: 5
          }
        ]
      },
      {
        title: 'User Experience en User Centered Design: Usertests',
        display_name: 'User Experience en User Centered Design: Usertests',
        description: 'User Experience en User Centered Design: Usertests',
        calculation_method: 'latest',
        mastery_points: 4,
        ratings: [
          {description: 'Verkennend - Je verdiept je in mogelijke UX methoden.', points: 1},
          {
            description: 'Werk in uitvoering - Je kan het nut van een usertest niet benoemen, legt niks vast in een testplan, voert geen usertest uit.',
            points: 2
          },
          {
            description: 'Twijfelachtig - Je hebt matig inzicht in het doel van de usertest en/of legt dit niet afdoende vast in een testplan, past een UX methode toe, maar kan de keuze helder onderbouwen, voert een usertest uit, maar de uitvoering is matig en/of trekt niet op de juiste manier conclusies. ',
            points: 3
          },
          {
            description: 'Geoefend - Je hebt duidelijk voor ogen wat je wil onderzoeken tijdens de usertest en legt dit vast in een testplan, kan de keuze voor een UX methode helder onderbouwen, voert de usertest op een juiste manier uit en rapporteert relevante resultaten en conclusies.',
            points: 4
          },
          {
            description: 'Uitstekend - Je onderbouwt waarom de gekozen UX methode de juiste is om je vraag te beantwoorden, reflecteert kritisch op uitvoering (proces), de resultaten en getrokken conclusies.',
            points: 5
          }
        ]
      }
    ]
  },
  {
    title: 'Design & Development',
    description: 'De student kan een media-installatie realiseren op basis van elegante code en technieken waarin data en audiovisuele elementen elkaar versterken en vanuit een ontwerp voor passende devices en hardware.',
    outcomes: [
      {
        title: 'Design en Development: Programmeren',
        display_name: 'Design en Development: Programmeren',
        description: 'Design en Development: Programmeren',
        calculation_method: 'latest',
        mastery_points: 4,
        ratings: [
          {description: 'Verkennend - Je hebt onderzocht welke (nieuwe) mogelijkheden er zijn en neemt voorbeelden over. ', points: 1},
          {
            description: 'Werk in uitvoering - Je hebt werkende of gedeeltelijk werkende code maar kan geen onderbouwing geven over de werking en de samenhang.',
            points: 2
          },
          {
            description: 'Twijfelachtig - Je past vooral bestaande code aan, schrijft functionerende code en kan eigen toevoegingen verklaren en uitleggen, de code functioneert maar is niet de meest logische/efficiente oplossing.',
            points: 3
          },
          {
            description: 'Geoefend - Je hebt inzicht in eigen code en voegt naar behoefte code/features toe.',
            points: 4
          },
          {
            description: 'Uitstekend - Je schrijft werkende eigen code, deze code is efficiënt en elegant, de student is kritisch op eigen werk en werkt met de instelling dat elke uitwerking beter kan.',
            points: 5
          }
        ]
      },
      {
        title: 'Design en Development: Audiovisueel ontwerp',
        display_name: 'Design en Development: Audiovisueel ontwerp',
        description: 'Design en Development: Audiovisueel ontwerp',
        calculation_method: 'latest',
        mastery_points: 4,
        ratings: [
          {description: 'Verkennend - Je hebt meerdere onderzoeksmethodes gebruikt om te achterhalen welke vormtaal bij installaties mogelijk gebruikt kunnen worden.', points: 1},
          {
            description: 'Werk in uitvoering - Je kan een productontwerp schetsen dat de werking en ervaring van het product of de media-installatie illustreert, maar er is nog nauwelijks een iteratie gedaan.',
            points: 2
          },
          {
            description: 'Twijfelachtig - Je hebt iteratief ontworpen, maar de verschillende onderdelen hebben weinig samenhang, de keuzes van de audiovisuele elementen zijn niet relevant voor het onderwerp.',
            points: 3
          },
          {
            description: 'Geoefend - Je werkt het ontwerp uit in een prototype waarmee je de samenhang en audiovisuele ontwerpkeuzes kan onderbouwen.',
            points: 4
          },
          {
            description: 'Uitstekend - Je ontwerp is volledig uitgewerkt en je kan alle ontwerpkeuzes onderbouwen, er zijn verschillende media in sterke samenhang toegepast.',
            points: 5
          }
        ]
      },
      {
        title: 'Design en Development: Hardware en data',
        display_name: 'Design en Development: Hardware en data',
        description: 'Design en Development: Hardware en data',
        calculation_method: 'latest',
        mastery_points: 4,
        ratings: [
          {description: 'Verkennend - Je begrijpt hoe hardware, devices, code en data ingezet kunnen worden om een media-installatie te realiseren.', points: 1},
          {
            description: 'Werk in uitvoering - Je eigen keuze voor hardware en devices bevat gemiste kansen, dataverwerking en -communicatie werkt niet of is niet geautomatiseerd.',
            points: 2
          },
          {
            description: 'Twijfelachtig - Je mist passende hardware voor je concept, de front-end is ontworpen voor het gekozen device, er is werkende maar niet elegante communicatie en verwerking van data.',
            points: 3
          },
          {
            description: 'Geoefend - Je keuze voor de hardware is passend voor je concept, de front-end is ontworpen voor het gekozen device, dataverwerking en -communicatie is elegant en werkend.',
            points: 4
          },
          {
            description: 'Uitstekend - Je keuze voor de gebruikte hardware is passend voor je concept, de front-end werkt passend op verschillende devices, de communicatie en verwerking van data in de installatie is complex en elegant.',
            points: 5
          }
        ]
      }
    ]
  },
  {
    title: 'Onderzoek',
    description: 'De student kan de regie voeren over zijn experimenteerproces, waarbij er gedurfde technieken uit het gehele landschap ingezet worden. Hierbij wordt gereflecteerd op gemaakte keuzes en onderzoeksmethodiek.',
    outcomes: [
      {
        title: 'Onderzoek: Onderzoekscyclus',
        display_name: 'Onderzoek: Onderzoekscyclus',
        description: 'Onderzoek: Onderzoekscyclus',
        calculation_method: 'latest',
        mastery_points: 4,
        ratings: [
          {description: 'Verkennend - Je begrijpt de verschillende onderzoeksmethoden, het begrip triangulatie en de onderzoekscyclus voor iteratief werken.', points: 1},
          {
            description: 'Werk in uitvoering - Je kan onder begeleiding een experiment definiëren door het stellen van onderzoeksvragen en het kiezen van geschikte onderzoeksmethodieken.',
            points: 2
          },
          {
            description: 'Twijfelachtig - Je beschrijft per dag welke vraag je onderzoekt en bedenkt in overleg welke methodes je het beste kan toepassen, je beschrijft je resultaten en kan in overleg het volgende experiment vormgeven.',
            points: 3
          },
          {
            description: 'Geoefend - Je kan per dag zelfstandig beschrijven welke vraag je onderzoekt in een experiment, welke methodieken je toepast en door reflectie op de resultaten het volgende experiment vormgeven.',
            points: 4
          },
          {
            description: 'Uitstekend - Je kan per dag zelfstandig beschrijven welke vraag je onderzoekt in een experiment, welke onderbouwde methodieken je toepast als triangulatie en door reflectie op de resultaten het volgende experiment vormgeven.',
            points: 5
          }
        ]
      },
      {
        title: 'Onderzoek: Onderzoekende houding',
        display_name: 'Onderzoek: Onderzoekende houding',
        description: 'Onderzoek: Onderzoekende houding',
        calculation_method: 'latest',
        mastery_points: 4,
        ratings: [
          {description: 'Verkennend - Je observeert, maar zet dit niet om in acties.', points: 1},
          {
            description: 'Werk in uitvoering - Je reflecteert op je eigen handelen, maar niet op dat van anderen, je laat onverwachte omstandigheden je proces stoppen, je onderzoekt nog niet naar innovatieve processen en producten.',
            points: 2
          },
          {
            description: 'Twijfelachtig - Je reflecteert op je eigen handelen en hebt op aangeven van de docent het werk van anderen van feedback voorzien, je legt de verantwoordelijkheid van wisselende omstandigheden buiten jezelf, je onderzoek kan uitgewerkt worden in innovatieve processen en producten.',
            points: 3
          },
          {
            description: 'Geoefend - Je reflecteert op eigen initiatief op je eigen handelen en geeft zinvolle feedback op het werk van anderen, je past je product aan op onverwachte omstandigheden, je richt je onderzoek op innovatieve processen en producten.',
            points: 4
          },
          {
            description: 'Uitstekend - Je reflecteert op eigen initiatief op je handelen en dat van anderen om tot betere prestaties te komen, je kan constructief omgaan met onverwachte en wisselende omstandigheden, je onderzoek komt tot uiting in innovatieve processen en producten.',
            points: 5
          }
        ]
      }
    ]
  }
];

var courseId = '1276',
  baseUrl = 'https://fhict.instructure.com';

function getOutcomeGroups(courseId, callback) {
  var apiUrl = baseUrl + "/api/v1/courses/" + courseId + '/outcome_groups';

  $.get(apiUrl, function (a) {
    callback(a);
  })
}

function createOutcomeSubGroup(courseId, outcomeGroupId, title, description, callback) {
  var apiUrl = baseUrl + '/api/v1/courses/' + courseId + '/outcome_groups/' + outcomeGroupId + '/subgroups';
  var params = {
    title: title,
    description: description
  };

  $.post(apiUrl, params, function (a) {
    callback(a);
  })
}

function processArray(array, index, processItem, callback) {
  processItem(array[index], function () {
    if (++index === array.length) {
      callback();
      return;
    }
    processArray(array, index, processItem, callback);
  });
}

function createOutcome(courseId, outcomeGroupId, outcomeData, callback) {
  var apiUrl = baseUrl + '/api/v1/courses/' + courseId + '/outcome_groups/' + outcomeGroupId + '/outcomes',
    params = 'title=' + encodeURI(outcomeData.title) + '&display_name=' + encodeURI(outcomeData.display_name) + '&description=' + encodeURI(outcomeData.description) + '&mastery_points=' + outcomeData.mastery_points + '&calculation_method=' + outcomeData.calculation_method;

  outcomeData.ratings.forEach(function (rating) {
    params += '&ratings[][description]=' + encodeURI(rating.description) + '&ratings[][points]=' + rating.points;
  });

  console.log('DATA:', apiUrl, params);

  $.post(apiUrl, params, function (a) {
    callback(a);
  })
}


getOutcomeGroups(courseId, function (outcomeGroups) {

  processArray(outcomeData, 0, function (group, callback) {
    console.log(group);
    createOutcomeSubGroup(courseId, outcomeGroups[0].id, group.title, group.description, function (newGroup) {
      console.log(newGroup);
      processArray(group.outcomes, 0, function (outcome, innerCallback) {
        console.log(outcome);
        createOutcome(courseId, newGroup.id, outcome, function (o) {
          console.log(o);
          innerCallback();
        })
      }, callback);
    });
  });

});
