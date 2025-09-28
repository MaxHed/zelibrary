import { useApi } from '@/composable/useApi'
interface Book { id: number; title: string }

export function useBooks() {
  const { get } = useApi()
  // On évite d'envoyer des en-têtes personnalisés côté client pour ne pas déclencher de preflight

  const books = useState("books", () => [] as Book[]);
  const totalItems = useState("booksTotal", () => 0);
  const loading = useState("booksLoading", () => false);
  const error = useState<string | null>("booksError", () => null);

  const fetchBooks = async () => {
    try {
      loading.value = true;
      error.value = null;
      const res = await get('/books/', {}, true);

      // On vérifie que res est bien un objet avant d'accéder à ses propriétés
      if (typeof res !== 'object' || res === null) {
        throw new Error("Réponse inattendue du serveur");
      }

      const member = (res as any)["hydra:member"] ?? (res as any).member ?? [];
      const total = (res as any)["hydra:totalItems"] ?? (res as any).totalItems ?? member.length;
      books.value = member as Book[];
      totalItems.value = total as number;
    } catch (e: any) {
      error.value = e?.message ?? "Erreur de chargement";
    } finally {
      loading.value = false;
    }
  };

  return { books, totalItems, loading, error, fetchBooks };
}
