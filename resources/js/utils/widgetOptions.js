// Widget Types - (When adding to this list, make sure to create the template)
export const widgetOptions = [
    { id: 1, name: 'cards', variant: 'cards_2_across', description: '2 cards in a row', label: 'Cards 2 Across' , path: 'cards/Cards2Across', hasHeader: true},
    { id: 2, name: 'cards', variant: 'cards_3_across', description: '3 cards in a row', label: 'Cards 3 Across' , path: 'cards/Cards3Across', hasHeader: true },
    { id: 3, name: 'cards', variant: 'cards_4_across', description: '4 cards in a row', label: 'Cards 4 Across' , path: 'cards/Cards4Across', hasHeader: true },
    { id: 4, name: 'side_by_side', variant: 'side_by_side_default', description: 'Show a text paragraph next to a big image', label: 'Side by Side' , path: 'sidebyside/SideBySide' , hasHeader: true},
    { id: 5, name: 'side_by_side', variant: 'side_by_side_full', description: 'Show a text paragraph next to a big image (full screen)', label: 'Side by Side (Full Screen)' , path: 'sidebyside/SideBySideFull', hasHeader: true},
    { id: 6, name: 'cards_wide', variant: 'cards_wide_2', description: '2 cards with larger widths than regular cards 2 across', label: 'Cards Wide 2' , path: 'cardswide/Cards2Across', hasHeader: true},
    { id: 7, name: 'cards_wide', variant: 'cards_wide_3', description: '2 cards with larger widths than regular cards 3 across', label: 'Cards Wide 3' , path: 'cardswide/Cards3Across', hasHeader: true },
    { id: 8, name: 'mosaic', variant: 'mosaic_4', description: 'A larger image on the left then 3 smaller images stacked on the right', label: 'Moasic 4' , path: 'mosaic/Mosaic4', hasHeader: true},
    { id: 8, name: 'hero', variant: 'hero_image', description: 'A big image banner for the top of the page', label: 'Hero Image' , path: 'hero/HeroImage' , hasHeader: false},
    { id: 8, name: 'hero', variant: 'hero_image_alt', description: 'A big image banner for the top of the page', label: 'Hero Image Alt' , path: 'hero/HeroImageAlt' , hasHeader: false},
];