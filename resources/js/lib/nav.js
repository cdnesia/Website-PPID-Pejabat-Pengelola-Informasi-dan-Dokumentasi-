export function parseLinkTarget(to) {
    const [pathAndQuery, hash = ''] = to.split('#');
    const [path, queryString = ''] = pathAndQuery.split('?');
    const query = Object.fromEntries(new URLSearchParams(queryString));
    return { path, query, hash };
}

export function isLinkActive(route, to) {
    const { path, query, hash } = parseLinkTarget(to);
    if (route.path !== path) return false;

    if (hash) {
        return route.hash === `#${hash}`;
    }

    const queryKeys = Object.keys(query);
    if (queryKeys.length === 0) {
        return Object.keys(route.query).length === 0;
    }

    return queryKeys.every((key) => route.query[key] === query[key]);
}
