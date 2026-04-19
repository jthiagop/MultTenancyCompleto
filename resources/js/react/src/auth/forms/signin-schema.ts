import { z } from 'zod';

export const getSigninSchema = () => {
  return z.object({
    email: z
      .string()
      .min(1, { message: 'Informe o e-mail.' })
      .email({ message: 'E-mail inválido.' }),
    password: z.string().min(1, { message: 'Informe a senha.' }),
    rememberMe: z.boolean().optional(),
  });
};

export type SigninSchemaType = z.infer<ReturnType<typeof getSigninSchema>>;
