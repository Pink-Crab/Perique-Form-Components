import type { BlueprintBundle } from './types';
export declare class BlueprintFetchError extends Error {
    readonly url: string;
    constructor(message: string, url: string, options?: ErrorOptions);
}
/**
 * Resolves a remote blueprint from a URL.
 *
 * @param url - The URL of the blueprint to resolve.
 * @returns A promise that resolves to the resolved blueprint.
 */
export declare function resolveRemoteBlueprint(url: string): Promise<BlueprintBundle>;
