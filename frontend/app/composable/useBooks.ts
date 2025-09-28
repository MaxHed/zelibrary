import { useApi } from '@/composable/useApi'
interface Book { id: number; title: string }

export function useBooks() {
  const { apiCall } = useApi()
  // On évite d'envoyer des en-têtes personnalisés côté client pour ne pas déclencher de preflight

  const books = useState("books", () => [] as Book[]);
  const totalItems = useState("booksTotal", () => 0);
  const loading = useState("booksLoading", () => false);
  const error = useState<string | null>("booksError", () => null);

  const fetchBooks = async () => {
    try {
      loading.value = true;
      error.value = null;
      const res = await apiCall<any>('GET', '/books/');
      const member = res["hydra:member"] ?? res.member ?? [];
      const total = res["hydra:totalItems"] ?? res.totalItems ?? member.length;
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
