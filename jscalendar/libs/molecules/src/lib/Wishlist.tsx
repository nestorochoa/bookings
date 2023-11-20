import { FC } from 'react';
import styled from 'styled-components';
import { BookingCard } from './BookingCard';

export interface WishlistProps {
  wishlistEvents: Array<any>;
}
const StyledWishList = styled.div`
  .wishlistContainer {
    max-height: 30rem;
    overflow: auto;
  }
`;
export const Wishlist: FC<WishlistProps> = ({ wishlistEvents }) => {
  return (
    <StyledWishList>
      <h3>Wishlist</h3>
      <div className="wishlistContainer">
        {wishlistEvents.map((elm, index) => (
          <BookingCard
            {...elm}
            key={`wl-${index}`}
            wishlist={true}
          ></BookingCard>
        ))}
      </div>
    </StyledWishList>
  );
};
