/**
 * Submissão clássica Laravel: POST com campos hidden (equivalente a @csrf + form).
 * O navegador segue o redirect (302) e exibe flash/erros na próxima página.
 */
export function submitLaravelPostForm(
  action: string,
  fields: Record<string, string | boolean | undefined>,
): void {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = action;

  for (const [name, value] of Object.entries(fields)) {
    if (value === undefined || value === false) continue;
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value === true ? '1' : String(value);
    form.appendChild(input);
  }

  document.body.appendChild(form);
  form.submit();
}
