export function getFlagClass(code: string): string {
  return `fi fi-${code.toLowerCase()}`;
}

export function imageForCountry(code: string | undefined | null): string | null {
  if (!code) {
    return null;
  }
  const modules = import.meta.glob('@/images/destinations/*.{jpg,jpeg,png,webp}', {
    eager: true,
    import: 'default',
  });

  const destinationImages: Record<string, string> = Object.fromEntries(
    Object.entries(modules).map(([path, url]) => {
      const code = path.split('/').pop()!.split('.')[0].toUpperCase();
      return [code, url as string];
    }),
  );

  return destinationImages[code.toUpperCase()] ?? null;
}
