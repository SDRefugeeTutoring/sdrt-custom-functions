export interface UpcomingEvents {
    categories: {
        k5: Category;
        middle: Category;
        other: Category;
    };
    trimesters: Array<Category>;
}

interface Category {
    id: number;
    name: string;
    slug: string;
}
